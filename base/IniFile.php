<?php
/**
 * Copyright (c) 2009 Coen Bijlsma
 *
 * This file is part of phpbase.
 *
 *  phpbase is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  phpbase is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with phpbase.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Coen Bijlsma
 */
class IniFile {
    private $file_path = ''; 
    /**
     * @var array
     */
    private $data;

    /**
     * @var IniFile
     */
    private static $config = null;

    public function __construct($file_path)   {
		$this->file_path = $file_path ;
        $this->data = parse_ini_file( $this->file_path , true);
    }

    public static function get($section, $key)   {
        if (!is_string($section))    {
            throw new Exception('Not a string: $section');
        }   elseif (!is_string($key))     {
             throw new Exception('Not a string: $key');
        }

        $config = self::getConfig();

        if (!empty($section))
        {
            $array = $config->data[$section];
            return $array[$key];
        }
        else
        {
            return $config->data[$key];
        }
    }

    public static function getBool($section, $key)   {
        if (!is_null($section) && !is_string($section)){
            throw new Exception('Not a string: $section');
        }
        if (!is_string($key))
        {
            throw new Exception('Not a string: $key');
        }
        $value = trim(self::get($section, $key));

        if (!is_null($value) && strlen($value) > 0)
        {
            $value = strtolower($value);

            switch($value)
            {
                case "false":
                case "0":
                case "no":
                case "off":
                    return false;
                default:
                    return true;
            }
        }
        else
        {
            throw new Exception('No such section/value: ' . $section . ':' . $key);
        }
    }

    public static function set($section, $key, $value)   {
        $config = self::getConfig();

        if (!empty($section))
        {
            return $config->data[$section][$key] = $value;
        }
        else
        {
            return $config->data[$key] = $value;
        }
    }

    public static function write()    {
        $res = array();
        $config = self::getConfig();
        $array = $config->data;

        foreach($array as $key => $val)
        {
            if(is_array($val))
            {
                $res[] = "[$key]";
                foreach($val as $skey => $sval)
                {
                    $res[] = "$skey = ".(is_numeric($sval) ? $sval : '"'.$sval.'"');
                }
            }
            else
            {
                $res[] = "$key = ".(is_numeric($val) ? $val : '"'.$val.'"');
            }
        }
        self::safeFileRewrite( $this->file_path , implode(PHP_EOL, $res));
    }

    public static function exists()   {
        return file_exists( $this->file_path);
    }

    private static function safeFileRewrite($filename, $data)   {
        $fp = fopen($filename, 'w');

        if ($fp)     {
            $start_time = microtime();
            do  {
                $can_write = flock($fp, LOCK_EX);
                // If lock not obtained sleep for 0 - 100 milliseconds
                // , to avoid collision and CPU load
                if(!$can_write) usleep(round(rand(0, 100)*1000));
            } while ((!$can_write)and((microtime()-$start_time) < 1000));

            //file was locked so now we can store information
            if ($can_write)    {
                fwrite($fp, $data);
                flock($fp, LOCK_UN);
            }

            fclose($fp);
        }

    }

    /**
     *
     * @return IniFile
     */
    private static function getConfig()  {
        if (is_null(self::$config))
        {
            self::$config = new IniFile();
        }
        return self::$config;
    }
}


//usage 
/*

settings.ini
======================
[General]
url = "http://www.example.com"
[Database]
host = localhost
username = user
password = password
db = cms
adapter = mysqli

=========================
<?php 
$ini = IniFile::getInstance('/path/to/settings.ini');
echo $ini->url;
print_r($ini->Database);
echo $ini->db;

$ini = new IniFile('/path/to/settings.ini');
echo $ini->url;
print_r($ini->Database);
?>

===========================
http://www.example.com
Array
(
    [host] => localhost
    [username] => user
    [password] => password
    [db] => cms
    [adapter] => mysqli
)
cms

class IniFile{
	
    private static $instance;
    private $settings;
   
    private function __construct($ini_file) {
        $this->settings = parse_ini_file($ini_file, true);
    }
   
    public static function getInstance($ini_file) {
        if(! isset(self::$instance)) {
            self::$instance = new Settings($ini_file);           
        }
        return self::$instance;
    }
   
    public function __get($setting) {
        if(array_key_exists($setting, $this->settings)) {
            return $this->settings[$setting];
        } else {
            foreach($this->settings as $section) {
                if(array_key_exists($setting, $section)) {
                    return $section[$setting];
                }
            }
        }
    }
	
	public function safefilerewrite($fileName, $dataToSave){   
	 if ($fp = fopen($fileName, 'w')) {
        $startTime = microtime();
        do{            $canWrite = flock($fp, LOCK_EX);
           // If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
           if(!$canWrite) usleep(round(rand(0, 100)*1000));
        } while ((!$canWrite)and((microtime()-$startTime) < 1000));
        //file was locked so now we can store information
        if ($canWrite){            
			fwrite($fp, $dataToSave);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

}
	
}*/
?>