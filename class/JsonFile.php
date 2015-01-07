<?

class JsonFile {
    protected $data = array();
    private $file ;
    public function __construct($file) {
        $this->file =  $file;
        if (is_file($file)) {
            $str = file_get_contents($file);
            $this->data = json_decode($str, true);
        }
        register_shutdown_function(function(){
            $this->write() ;
        }) ;
    }
    public function remove($key){
        unset($this->data[$key]) ;
    }
    public function __get($key) { //$section
        if (isset($this->data[$key])) return  $this->data[$key] ;
        return -1 ;
    }
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }
    public function write() {
        // echo 'Saving '.$this->file ;
        $fh = fopen($this->file, 'w') ;
        if (count($this->data))
            fwrite($fh, json_encode($this->data,JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        fclose($fh);
    }
}
?>