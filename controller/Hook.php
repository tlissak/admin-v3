<?php

class Hook{

    static $cont = array("action"=>'' ,'css'=>'','js'=>'','tabscont'=>'','tabs'=>'','dashboard'=>'','controls'=>'','menu'=>'','footer'=>'') ;
    //Actions list

    static function Add($type,$cont){         self::$cont[$type] .= $cont . NL ;    }

    static function Css(){ return self::$cont['css'] ; }
    static function Js(){ return self::$cont['js'] ; }
    static function TabsCont(){return self::$cont['tabscont'] ; }
    static function Tabs(){return self::$cont['tabs'] ; }
    static function Dashboard(){return self::$cont['dashboard'] ; }
    static function Controls(){return self::$cont['controls'] ; }
    static function Menu(){return self::$cont['menu'] ; }
    static function Footer(){return self::$cont['footer'] ; }
    static function Action(){
        $actions = explode(NL,self::$cont['action']) ;
        foreach($actions as $action){
            if ($action) {
                $action();
            }
        }
    }

}