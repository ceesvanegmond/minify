<?php namespace CeesVanEgmond\Minify;

use Config;
use CssMin;
use Exception;
use JSMin;
use File;

class Minify {

    /**
     * $files
     *
     * @var mixed
     *
     * @access protected
     */
	protected $files;

    /**
     * $buildpath
     *
     * @var mixed
     *
     * @access protected
     */
	protected $buildpath;

    /**
     * $path
     *
     * @var mixed
     *
     * @access protected
     */
	protected $path;

    /**
     * minifyCss
     * 
     * @param mixed $files Description.
     *
     * @access public
     * @return mixed Value.
     */
	public function minifyCss($files)
	{
		$this->files = $files;
		$this->path = public_path() . Config::get('minify::css_path');		
		$this->buildpath = $this->path . Config::get('minify::css_build_path');
		
		$this->createBuildPath();	
				
		$totalmod = $this->doFilesExistReturnModified();

		$filename = md5(str_replace('.css', '', implode('-', $this->files)) . '-' . $totalmod).'.css';
		$output = $this->buildpath . $filename;

		if ( file_exists($output) ) {
			return $this->absoluteToRelative($output);
		}

		$all = $this->appendAllFiles();	
		$result = CssMin::minify($all);		

		file_put_contents($output, $result);

		return $this->absoluteToRelative($output);
	}

  	/**
     * minifyJs
     * 
     * @param mixed $files Description.
     *
     * @access public
     * @return mixed Value.
     */
	public function minifyJs($files)
	{
		$this->files = $files;
		$this->path = public_path() . Config::get('minify::js_path');		
		$this->buildpath = $this->path . Config::get('minify::js_build_path');

		$this->createBuildPath();	
				
		$totalmod = $this->doFilesExistReturnModified();

		$filename = md5(str_replace('.js', '', implode('-', $this->files)) . '-' . $totalmod).'.js';
		$output = $this->buildpath . $filename;

		if ( file_exists($output) ) {
			return $this->absoluteToRelative($output);
		}
		
		$all = $this->appendAllFiles();	
		$result = JSMin::minify($all);		

		file_put_contents($output, $result);

		return $this->absoluteToRelative($output);
	}

    /**
     * createBuildPath
     * 
     * @access private
     * @return mixed Value.
     */
	private function createBuildPath()
	{		
		if ( ! File::isDirectory($this->buildpath) ) {
			File::makeDirectory($this->buildpath);
		}
	}

    /**
     * absoluteToRelative
     * 
     * @param mixed $url Description.
     *
     * @access private
     * @return mixed Value.
     */
	private function absoluteToRelative($url)
	{
		return '//' . $this->remove_http(\URL::asset(str_replace(public_path(), '', $url)));
	}

    /**
     * remove_http
     * 
     * @param mixed $url Description.
     *
     * @access private
     * @return mixed Value.
     */
    private function remove_http($url) {
        $disallowed = array('http://', 'https://');
        foreach($disallowed as $d) {
            if(strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }
        return $url;
    }

    /**
     * appendAllFiles
     * 
     * @access private
     * @return mixed Value.
     */
	private function appendAllFiles()
	{		
		$all = '';
		foreach ($this->files as $file)
			$all .= File::get($this->path . $file);

		if ( ! $all ) {
			throw new Exception;
		}

		return $all;
	}
    /**
     * doFilesExistReturnModified
     * 
     * @access private
     * @return mixed Value.
     */
	private function doFilesExistReturnModified()
	{
		if (!is_array($this->files))
			$this->files = array($this->files);
	
		$filetime = 0;
				
		foreach ($this->files as $file) {
			$absolutefile = $this->path . $file;

			if ( ! File::exists($absolutefile)) {			
				throw new Exception;
			}

			$filetime += File::lastModified($absolutefile);

		}

		return $filetime;
	}

}