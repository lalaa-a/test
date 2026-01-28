<?php

    /**
     * extractSection
     * $content = return object type of ob_start() ob_get_clean() basically loaded php content
     * @param string $section -> whether we need HTML,CSS,JS
     */


    function extractSection($content, $section) {
        switch($section) {
            case 'HTML':
                // Extract everything except <style> and <script> tags
                $html = preg_replace('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', '', $content);
                $html = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $html);
                return $html;
                
            case 'CSS':
                // Extract content between <style> tags
                preg_match_all('/<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>/mi', $content, $matches);
                $css = '';
                foreach($matches[0] as $match) {
                    $css .= strip_tags($match);
                }
                return $css;
                
            case 'JS':
                // Extract content between <script> tags
                preg_match_all('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', $content, $matches);
                $js = '';
                foreach($matches[0] as $match) {
                    $js .= strip_tags($match);
                }
                return $js;
        }
        return '';
    }


function getCSS($folder,$fileName){
    return file_get_contents(URL_ROOT.'/public/css/'.$folder.'/'.$fileName.'.css');
}

function getJS($folder,$fileName){
    return file_get_contents(URL_ROOT.'/public/js/'.$folder.'/'.$fileName.'.js');
}
