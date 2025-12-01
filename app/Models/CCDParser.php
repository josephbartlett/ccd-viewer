<?php
namespace App\Models;

use DOMDocument;
use DOMXPath;

/**
 * CCDParser loads and processes a CCD/CCDA XML file into a structured array
 * for display by the viewer. This is not a comprehensive CCD parser, but
 * extracts key metadata and section entries for demonstration purposes.
 */
class CCDParser
{
    /**
     * Parse a CCD/CCDA XML file.
     *
     * @param string $filePath Path to the XML file on disk.
     * @return array Structured representation of the CCD.
     */
    public function parse(string $filePath): array
    {
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;

        if (!@$doc->load($filePath)) {
            throw new \RuntimeException('Unable to load XML file.');
        }

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('cda', 'urn:hl7-org:v3');

        $metadata = $this->extractMetadata($xpath);
        $sections = $this->extractSections($xpath);
        $rawXml = $doc->saveXML();

        return [
            'metadata' => $metadata,
            'sections' => $sections,
            'rawXml'   => $rawXml,
        ];
    }

    /**
     * Extract core metadata such as patient information and document details.
     *
     * @param DOMXPath $xpath
     * @return array
     */
    private function extractMetadata(DOMXPath $xpath): array
    {
        $metadata = [];
        // Title
        $title = $xpath->evaluate('string(/cda:ClinicalDocument/cda:title)');
        $metadata['title'] = $title ?: 'Clinical Document';

        // Effective time
        $effectiveTime = $xpath->evaluate('string(/cda:ClinicalDocument/cda:effectiveTime/@value)');
        if ($effectiveTime) {
            $metadata['effectiveTime'] = $this->formatDate($effectiveTime);
        }

        // Patient info
        $patient = [];
        $given = $xpath->evaluate('string(/cda:ClinicalDocument/cda:recordTarget/cda:patientRole/cda:patient/cda:name/cda:given)');
        $family = $xpath->evaluate('string(/cda:ClinicalDocument/cda:recordTarget/cda:patientRole/cda:patient/cda:name/cda:family)');
        $patient['name'] = trim($given . ' ' . $family);

        $dob = $xpath->evaluate('string(/cda:ClinicalDocument/cda:recordTarget/cda:patientRole/cda:patient/cda:birthTime/@value)');
        if ($dob) {
            $patient['dob'] = $this->formatDate($dob);
        }

        $gender = $xpath->evaluate('string(/cda:ClinicalDocument/cda:recordTarget/cda:patientRole/cda:patient/cda:administrativeGenderCode/@code)');
        if ($gender) {
            $patient['gender'] = $gender;
        }

        $metadata['patient'] = $patient;

        return $metadata;
    }

    /**
     * Extract sections and their entries.
     *
     * @param DOMXPath $xpath
     * @return array
     */
    private function extractSections(DOMXPath $xpath): array
    {
        $sections = [];
        $sectionNodes = $xpath->query('/cda:ClinicalDocument/cda:component/cda:structuredBody/cda:component/cda:section');

        if ($sectionNodes !== false) {
            $index = 0;
            foreach ($sectionNodes as $section) {
                $index++;
                // Title of section
                $title = $xpath->evaluate('string(cda:title)', $section);
                // Code and code system
                $code = $xpath->evaluate('string(cda:code/@code)', $section);
                $codeSystem = $xpath->evaluate('string(cda:code/@codeSystemName)', $section);
                $narrative = $this->extractNarrative($section, $xpath);
                $entries = $this->extractEntries($section, $xpath);

                $sections[] = [
                    'id' => 'section-' . $index,
                    'title' => $title ?: $code ?: 'Unnamed Section',
                    'code' => $code,
                    'codeSystem' => $codeSystem,
                    'narrative' => $narrative,
                    'entries' => $entries,
                ];
            }
        }
        return $sections;
    }

    /**
     * Extract the human-readable narrative from a section. Returns HTML.
     *
     * @param \DOMNode $section
     * @param DOMXPath $xpath
     * @return string
     */
    private function extractNarrative($section, DOMXPath $xpath): string
    {
        // The narrative is typically in the <text> element
        $textNode = $xpath->query('cda:text', $section)->item(0);
        if ($textNode) {
            // Import as string; avoid processing
            $innerHTML = '';
            foreach ($textNode->childNodes as $child) {
                $innerHTML .= $textNode->ownerDocument->saveXML($child);
            }
            return $innerHTML;
        }
        return '';
    }

    /**
     * Extract entries from a section. Each entry is represented with a label, code, and code system when available.
     *
     * @param \DOMNode $section
     * @param DOMXPath  $xpath
     * @return array
     */
    private function extractEntries($section, DOMXPath $xpath): array
    {
        $entries = [];
        $entryNodes = $xpath->query('cda:entry', $section);
        if ($entryNodes !== false) {
            $counter = 0;
            foreach ($entryNodes as $entry) {
                $counter++;
                $label = '';
                $code = '';
                $codeSystem = '';

                // Try to find a displayName or value to use as label
                $codeNode = $xpath->query('.//cda:code', $entry)->item(0);
                if ($codeNode) {
                    $label = $codeNode->getAttribute('displayName');
                    $code = $codeNode->getAttribute('code');
                    $codeSystem = $codeNode->getAttribute('codeSystemName');
                }
                // Fallback: look for value element
                if (!$label) {
                    $valueNode = $xpath->query('.//cda:value', $entry)->item(0);
                    if ($valueNode) {
                        $label = $valueNode->getAttribute('displayName') ?: $valueNode->nodeValue;
                        if (!$code) $code = $valueNode->getAttribute('code');
                        if (!$codeSystem) $codeSystem = $valueNode->getAttribute('codeSystemName');
                    }
                }
                if (!$label) {
                    $label = 'Entry ' . $counter;
                }

                $entries[] = [
                    'id' => uniqid('entry_', true),
                    'label' => $label,
                    'code' => $code,
                    'codeSystem' => $codeSystem,
                    'xmlSnippet' => $entry->ownerDocument->saveXML($entry),
                ];
            }
        }
        return $entries;
    }

    /**
     * Convert a YYYYMMDDHHMMSS or similar HL7 date into a human-readable format.
     *
     * @param string $value
     * @return string
     */
    private function formatDate(string $value): string
    {
        // HL7 timestamps: sometimes just YYYYMMDD or with time and optional timezone
        // We'll extract first 14 digits for YmdHis if available
        $pattern = '/^(\d{4})(\d{2})?(\d{2})?(\d{2})?(\d{2})?(\d{2})?/';
        if (preg_match($pattern, $value, $matches)) {
            $year = $matches[1];
            $month = $matches[2] ?? '01';
            $day = $matches[3] ?? '01';
            $hour = $matches[4] ?? '00';
            $minute = $matches[5] ?? '00';
            $second = $matches[6] ?? '00';
            $dt = date_create_from_format('YmdHis', $year . $month . $day . $hour . $minute . $second);
            if ($dt) {
                return $dt->format('Y-m-d H:i:s');
            }
        }
        return $value;
    }
}