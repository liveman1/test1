<?php
namespace NITSAN\NsYwSearch\Hooks;

/**
 * Hooks for ke_search
 */
class AdditionalPageFields
{
    public function modifyPageContentFields(&$fields)
    {
        $fields = 'description';
    }

    public function modifyContentFromContentElement(&$bodytext, &$ttContentRow)
    {
        $bodytext = $ttContentRow['bodytext'];
        $bodytext .= $ttContentRow['pi_flexform'];
    }
}
