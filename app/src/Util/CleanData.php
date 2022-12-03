<?php
declare(strict_types=1);

namespace App\Util;
/**
 * Sanitize user data
 */
class CleanData
{
    /**
     * @param $data
     *
     * @return string
     */
    public function cleanData($data): string
    {
        $data = strip_tags($data);
        $data = trim($data);
        $data = stripslashes($data);
        $data = addslashes($data);
        $data = htmlspecialchars($data);
        return htmlentities($data);
    }

}