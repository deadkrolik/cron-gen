<?php namespace Krolikoff\CronGen\Installer;

/**
 * Crontab installer for Unix
 */
class InstallerUnix implements Installer
{
	private $cmd = '/usr/bin/crontab';
	
	/**
	 * Save command lines as new crontab config
	 * @param array $lines Lines with commands and comments
	 * @throws \Exception
	 */
	public function install($lines)
	{
		$tmp_cron = tempnam(sys_get_temp_dir(), 'crogen');
		file_put_contents($tmp_cron, implode("\n", $lines));
		
		$output = array();
		exec($this->cmd.' '.$tmp_cron.' 2>&1', $output);
		
		@unlink($tmp_cron);
		
		//error output of crontab
		if (isset($output[0])) {
			throw new \Exception('Crontab reported error: '.$output[0]);
		}
	}
	
	/**
	 * Empty current crontab list
	 */
	public function clear()
	{
		exec($this->cmd.' -r 2>&1');
	}
}
