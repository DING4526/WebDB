<?php

namespace common\helpers;

class UploadHelper
{
    /**
     * 清洗文件基础名，移除危险字符，保证非空
     */
    public static function sanitizeBaseName($baseName, $default = 'file')
    {
        // 允许中文及其他文字字符、数字、下划线、短横线、空格与点号，其余替换为下划线
        $safe = preg_replace('/[^\p{L}\p{N}\s._-]/u', '_', (string)$baseName);
        $safe = trim($safe);
        return $safe === '' ? $default : $safe;
    }

    /**
     * 清洗扩展名，屏蔽高危后缀
     */
    public static function sanitizeExtension($extension)
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', (string)$extension);
        if ($clean === '') {
            return '';
        }
        $blocked = [
            'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'pht',
            'htaccess', 'ini', 'jsp', 'asp', 'aspx', 'exe', 'bat',
            'cmd', 'sh', 'pl', 'py', 'rb'
        ];
        return in_array(strtolower($clean), $blocked, true) ? 'txt' : $clean;
    }
}
