<?php

class GeneratorTest extends PHPUnit_Framework_TestCase
{
	public function testInitReturnsGeneratorObject()
	{
		$this->assertInstanceOf('\Krolikoff\CronGen\Generator', \Krolikoff\CronGen\Generator::init());
	}
	
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Unknown enviroment
	 */
	public function testConstructorFailsWithUnknownEnviroment()
	{
		new \Krolikoff\CronGen\Generator('OpapaOpapaTratata');
	}
	
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage String is not valid JSON
	 */
	public function testSetJsonFailsOnNotJson()
	{
		\Krolikoff\CronGen\Generator::init()->setJson('AAAAAAAAAAAAAAAAA');
	}
	
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Cannot find jobs or profiles
	 */
	public function testSetJsonFailsWhenProfilesMissing()
	{
		\Krolikoff\CronGen\Generator::init()->setJson(json_encode(array(
			'jobs' => array(),
		)));
	}
	
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Cannot find jobs or profiles
	 */
	public function testSetJsonFailsWhenJobsMissing()
	{
		\Krolikoff\CronGen\Generator::init()->setJson(json_encode(array(
			'profiles' => array(),
		)));
	}
	
	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage Profile is not found
	 */
	public function testGenerateFailsWhenProfileNotExists()
	{
		$generator = $this->getGenerator();
		$generator->setJson(json_encode(array(
			'jobs'     => array(),
			'profiles' => array(),
		)))->generate('UNKNOWN');
	}
	
	public function testGenerateExcludeDisabledJobs()
	{
		$generator = $this->getGenerator();
		$generator->setJson(json_encode(array(
			'jobs'     => array(
				array(
					'title'      => 'title1',
					'is_enabled' => true,
					'profiles'   => array('test'),
					'schedule'   => 'every 1 minute',
					'command'    => 'command1',
				),
				array(
					'title'      => 'title2',
					'is_enabled' => false,
					'profiles'   => array('test'),
					'schedule'   => 'every 2 minutes',
					'command'    => 'command2',
				),
				array(
					'title'      => 'title3',
					'is_enabled' => true,
					'profiles'   => array('test'),
					'schedule'   => 'every 3 minutes',
					'command'    => 'command3',
				),
			),
			'profiles' => array('test' => array()),
		)))->generate('test');
		$this->assertEquals(array('command1', 'command3'), $generator->getCommands());
	}
	
	public function testGenerateExcludeJobsNotInProfile()
	{
		$generator = $this->getGenerator();
		$generator->setJson(json_encode(array(
			'jobs'     => array(
				array(
					'title'      => 'title1',
					'is_enabled' => true,
					'profiles'   => array('profile_1'),
					'schedule'   => 'every 1 minute',
					'command'    => 'command1',
				),
				array(
					'title'      => 'title2',
					'is_enabled' => true,
					'profiles'   => array('profile_1'),
					'schedule'   => 'every 2 minutes',
					'command'    => 'command2',
				),
				array(
					'title'      => 'title3',
					'is_enabled' => true,
					'profiles'   => array('profile_2'),
					'schedule'   => 'every 3 minutes',
					'command'    => 'command3',
				),
				array(
					'title'      => 'title4',
					'is_enabled' => true,
					'profiles'   => array('profile_1', 'profile_2'),
					'schedule'   => 'every 4 minutes',
					'command'    => 'command4',
				),
			),
			'profiles' => array(
				'profile_1' => array(),
				'profile_2' => array(),
			),
		)))->generate('profile_1');
		$this->assertEquals(array('command1', 'command2', 'command4'), $generator->getCommands());
	}

	private function getGenerator()
	{
		//return command as whole crontab command, without any other things
		$parser = $this->getMock('\Krolikoff\CronGen\Parser\Parser', array('getRunConfig'));
		$parser->expects($this->any())->method('getRunConfig')
			->will($this->returnCallback(function($a, $b, $c) {
				return $b;
			}));
		
		$gen = new \Krolikoff\CronGen\Generator();
		$gen->setParser($parser);
		
		return $gen;
	}
}
