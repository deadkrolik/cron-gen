<?php namespace Krolikoff\CronGen;

class Generator
{
	/**
	 * @var object Decoded json string 
	 */
	private $json;
	
	/**
	 * @var array Commands to run
	 */
	private $commands = array();

	/**
	 * @var \CronGen\Parser\Parser Object to call parser
	 */
	private $parser;
	
	/**
	 * @var \CronGen\Installer\Installer Object to call installer
	 */
	private $installer;
	
	/**
	 * @param string $env Enviroment name
	 * @throws \Exception
	 */
	public function __construct($env = 'unix')
	{
		if ($env == 'unix') {
			$this->parser    = new Parser\ParserUnix();
			$this->installer = new Installer\InstallerUnix();
		}
		else {
			throw new \Exception('Unknown enviroment given');
		}
	}
	
	/**
	 * For static object creating \CronGen\Generator::init()-> .....
	 * @param string $env Enviroment name
	 * @return \CronGen\Generator
	 */
	public static function init($env = 'unix')
	{
		return new Generator($env);
	}
	
	/**
	 * Initialize generator with json-config
	 * @param string $file Filename or json-string with config
	 * @return \CronGen\Generator For chain calls
	 * @throws \Exception
	 */
	public function setJson($file)
	{
		$content = is_file($file) && is_readable($file) ? file_get_contents($file) : $file;
		
		$json = json_decode($content);
		if (!$json) {
			throw new \Exception('String is not valid JSON (code: "'.json_last_error().'")');
		}
		
		if (!isset($json->profiles) || !isset($json->jobs)) {
			throw new \Exception('Cannot find jobs or profiles section in JSON');
		}
		
		$this->commands = array();
		$this->json     = $json;
		
		return $this;
	}
	
	/**
	 * Generates commands for crontab from user json-config
	 * @param string $profile_name Profile name
	 * @return \Krolikoff\CronGen\Generator For chain calls
	 * @throws \Exception
	 */
	public function generate($profile_name)
	{
		if (!isset($this->json->profiles->$profile_name)) {
			throw new \Exception('Profile is not found in config ("'.$profile_name.'")');
		}
		
		$profile   = (object)$this->json->profiles->$profile_name;
		$variables = isset($profile->variables) ? $profile->variables : array();
		$commands  = array();
		
		foreach ($this->json->jobs as $job) {

            if (!isset($job->is_enabled)) {
                throw new \Exception('Job must have is_enabled property');
            }
            
			//job disabled or not for this profile
			if (!$job->is_enabled || !in_array($profile_name, $job->profiles)) {
				continue;
			}
			
			foreach($variables as $k => $v) {
				$job->command = str_replace('{'.$k.'}', $v, $job->command);
			}

			$commands[] = $this->parser->getRunConfig($job->schedule, $job->command, $job->title);
		}
		
		$this->commands = $commands;
		
		return $this;
	}
	
	/**
	 * Installs new crontab generated from config
	 * @return \CroGen\Generator For chain calls
	 * @throws \Exception
	 */
	public function install()
	{
		$this->installer->install($this->commands);
		
		return $this;
	}
	
	public function getCommands()
	{
		return $this->commands;
	}
	
	public function setParser($parser)
	{
		$this->parser = $parser;
	}
}
