<?php

class Flip {
    
    private $flipTable = [
        " " => "  ",
        "a" => "ɐ",
        "b" => "q",
        "c" => "ɔ",
        "d" => "p",
        "e" => "ǝ",
        "f" => "ɟ",
        "g" => "ƃ",
        "h" => "ɥ",
        "i" => "ı",
        "j" => "ɾ",
        "k" => "ʞ",
        "l" => "l",
        "m" => "ɯ",
        "n" => "u",
        "o" => "o",
        "p" => "d",
        "q" => "b",
        "r" => "ɹ",
        "s" => "s",
        "t" => "ʇ",
        "u" => "n",
        "v" => "ʌ",
        "w" => "ʍ",
        "x" => "x",
        "y" => "ʎ",
        "z" => "z",
        "!" => "¡",
        "?" => "¿",
        "," => "'",
        "." => "˙",
        "_" => "‾",
        "[" => "]",
        "{" => "}",
        "(" => ")",
    ];

    function __construct() {
        $flippedTable = [];
        foreach($this->flipTable as $key => $el) {
            $flippedTable[$el] = $key; 
        }
        
        $this->flipTable = array_merge($this->flipTable, $flippedTable);
    }

    public function flipText($text) {
        $text = strtolower(trim($text));
        $guyText = "(╯°□°）╯︵";
        $flippedText = " ┻━┻";

        if(!empty($text)) {
            $flippedText = ' ';
            $strlen = strlen($text);
            for( $i = 0; $i <= $strlen; $i++ ) {
                $char = substr( $text, $i, 1 );
                $flippedText .= isset($this->flipTable[$char]) ? $this->flipTable[$char] : '';
            }
        }

        return $guyText . $flippedText;
    }
}