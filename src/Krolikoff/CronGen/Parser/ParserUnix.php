<?php namespace Krolikoff\CronGen\Parser;

/**
 * Unix-formatted crontab run expressions
 */
class ParserUnix extends Parser
{
	//http://en.wikipedia.org/wiki/Cron
	private $formats = array(
		self::FORMAT_EVERY_X_MINUTES => '*/$1 * * * *',
		self::FORMAT_EVERY_HOUR_AT_X => '$1 * * * *',
		self::FORMAT_EVERY_DAY_AT_X  => '$2 $1 * * *',
	);
	
	/**
	 * Transforms time-format string into unix crontab notation with "stars"
	 * @param string $string Human friendly string, when to start a task
	 * @return string Cron specific format, for example "10 * * * *"
	 * @throws \Exception
	 */
	public function convertConfig($string)
	{
		$match_type = $this->matchRegexp($string);
		
		if (!isset($this->formats[$match_type])) {
			throw new \Exception('Unsupported match type');
		}
		
		$result  = $this->formats[$match_type];
		$matches = $this->getMatches();

		for($i=0;$i<count($matches);$i++) {
			$result = str_replace('$'.($i+1), (int)$matches[$i], $result);//to avoid "*/01" make it INT
		}
		
		return $result;
	}
	
	/**
	 * Returns string for writing into crontab file, for example:
	 *		#Comment is job name
	 *		1 * * * *  /path/to/executable
	 * @param string $schedule User friendly string (every 1 minute)
	 * @param string $command Command to run (/path/to/executable)
	 * @param string $title Job name
	 * @return string Crontab-formatter string
	 */
	public function getRunConfig($schedule, $command, $title)
	{
		return '#'.$title."\n".$this->convertConfig($schedule).' '.$command;
	}
}
