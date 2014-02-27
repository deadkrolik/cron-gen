<?php namespace Krolikoff\CronGen\Parser;

/**
 * Base class for all parsers
 */
class Parser
{
	const FORMAT_EVERY_X_MINUTES = 1;
	const FORMAT_EVERY_HOUR_AT_X = 2;
	const FORMAT_EVERY_DAY_AT_X  = 3;

	//which human readable strings we support
	protected $regexps = array(
		self::FORMAT_EVERY_X_MINUTES => 'every ([0-9]+) minutes?',
        self::FORMAT_EVERY_HOUR_AT_X => 'every ([0-9]+)(st|nd|rd|th) minute of every hour',
		self::FORMAT_EVERY_DAY_AT_X  => 'every day at ([0-9]+):([0-9]+)',
	);
	
	/**
	 * Found matches (numbers) for format regexp
	 * @var array
	 */
	private $matches;
	
	/**
	 * Is the string from config matches to defined formats
	 * @param string $string Human readable string, such as "every 10 minutes"
	 * @return int Format constant calue for config
	 * @throws \Exception
	 */
	public function matchRegexp($string)
	{
		$this->matches = array();
		$is_matched    = false;
        
        $string = preg_replace("|[ ]+|", ' ', $string);
		
		foreach ($this->regexps as $const => $match) {

			$is_matched = @preg_match("/^{$match}$/Ui", trim($string), $mt);
			if (!$is_matched) {
				continue;
			}
			
			break;
		}
		
		if (!$is_matched) {
			throw new \Exception('Cannot match expression "'.$string.'"');
		}
		
		$this->matches = array_slice($mt, 1, 99999);
		return $const;
	}
	
	/**
	 * Returns numbers found in regexp after matchRegexp() call
	 * @return array Matches in (...)
	 */
	public function getMatches()
	{
		return $this->matches;
	}
	
	//mock for child classes
	public function convertConfig($string)
	{
		return '';
	}
	
	//mock for child classes
	public function getRunConfig($schedule, $command, $title)
	{
		return '';
	}
}
