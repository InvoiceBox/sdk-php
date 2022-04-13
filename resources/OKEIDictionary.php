<?php
namespace Invoicebox\Resources;

class OKEIDictionary{

    public static function get_OKEI_data(){
        $okei_filepath = __DIR__ . "/okei.json";
        if(file_exists($okei_filepath)) return json_decode(file_get_contents($okei_filepath), true);
        return [];
    }

    public static function getMeasureByCode($code){
        $data = self::get_OKEI_data();
        if(is_array($data)){
            foreach ($data as $datum){
                if(!key_exists("CODE", $datum)) continue;
                if($code === $datum["CODE"]) return $datum["NATIONAL"];
            }

        }
        return null;
    }

    public static function getCodeByMeasure($measure){
        $data = self::get_OKEI_data();
        if(is_array($data)){
            foreach ($data as $datum){
                if(!key_exists("CODE", $datum)) continue;
                if($measure === $datum["NATIONAL"]) return $datum["CODE"];
            }

        }
        return null;
    }

    public static function measureExist($measure){
        $data = self::get_OKEI_data();
        if(is_array($data)){
            foreach ($data as $datum){
                if(!key_exists("CODE", $datum)) continue;
                if($measure === $datum["NATIONAL"]) return true;
            }

        }
        return false;
    }

    public static function codeExist($code){
        $data = self::get_OKEI_data();
        if(is_array($data)){
            foreach ($data as $datum){
                if(!key_exists("CODE", $datum)) continue;
                if($code === $datum["CODE"]) return true;
            }

        }
        return false;
    }

}