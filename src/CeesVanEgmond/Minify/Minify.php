<?php namespace CeesVanEgmond\Minify;

/**
* Minify
*
* @uses     
*
* @category Category
* @package  Package
*/
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
		$this->path = public_path() . \Config::get('minify::css_path');		
		$this->buildpath = $this->path . \Config::get('minify::css_build_path');
		
		$this->createBuildPath();	
				
		$totalmod = $this->doFilesExistReturnModified();

		$filename = str_replace('.css', '', implode('-', $this->files)) . '-' . md5($totalmod).'.css';
		$output = $this->buildpath . $filename;

		if ( \File::exists($output) ) {
			return $this->absoluteToRelative($output);
		}

		$all = $this->appendAllFiles();	
		$result = \CssMin::minify($all);		
		$this->cleanPreviousFiles($this->buildpath, $filename);

		\File::put($output, $result);

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
		$this->path = public_path() . \Config::get('minify::js_path');		
		$this->buildpath = $this->path . \Config::get('minify::js_build_path');

		$this->createBuildPath();	
				
		$totalmod = $this->doFilesExistReturnModified();

		$filename = str_replace('.js', '', implode('-', $this->files)) . '-' . md5($totalmod).'.js';
		$output = $this->buildpath . $filename;

		if ( \File::exists($output) ) {
			return $this->absoluteToRelative($output);
		}
		
		$all = $this->appendAllFiles();	
		$result = \JSMin::minify($all);		
		$this->cleanPreviousFiles($this->buildpath, $filename);

		\File::put($output, $result);

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
		if ( ! \File::isDirectory($this->buildpath) )
		{
			\File::makeDirectory($this->buildpath);
		}
	}

    /**
     * cleanPreviousFiles
     * 
     * @access private
     * @return mixed Value.
     */
	private function cleanPreviousFiles($dir, $filename)
	{
		$ext = \File::extension($filename);
		$filename = preg_replace('/[a-f0-9]{32,40}/','', $filename);
		$filename = str_replace('-.' . $ext, '', $filename);

		foreach (\File::files($dir) as $file)
		{
			if ( strpos($file, $filename) !== FALSE ) {
				\File::delete($file);
			}
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
		return \URL::asset(str_replace(public_path(), '', $url));		
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
			$all .= \File::get($this->path . $file);

		if ( ! $all )
			throw new Exception;

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

			if ( ! \File::exists($absolutefile)) {			
				throw new \Exception;
			}

			$filetime += \File::lastModified($absolutefile);

		}

		return $filetime;
	}

}