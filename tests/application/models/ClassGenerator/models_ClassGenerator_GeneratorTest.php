<?php


require_once 'application\models\ClassGenerator\Generator.php';

require_once 'PHPUnit\Framework\TestCase.php';


/**
 * models_ClassGenerator_Generator test case.
 */
class models_ClassGenerator_GeneratorTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var models_ClassGenerator_Generator
	 */
	private $models_ClassGenerator_Generator;


	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp()
	{
		parent::setUp();

		// TODO Auto-generated models_ClassGenerator_GeneratorTest::setUp()

		$this->models_ClassGenerator_Generator = new models_ClassGenerator_Generator(/* parameters */);

	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown()
	{
		// TODO Auto-generated models_ClassGenerator_GeneratorTest::tearDown()

		$this->models_ClassGenerator_Generator = null;

		parent::tearDown();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}


	/**
	 * Tests models_ClassGenerator_Generator->__construct()
	 */
	public function test__construct()
	{
		// TODO Auto-generated models_ClassGenerator_GeneratorTest->test__construct()
		$this->markTestIncomplete("__construct test not implemented");

		$this->models_ClassGenerator_Generator->__construct(/* parameters */);

	}

	/**
	 * Tests models_ClassGenerator_Generator->ausFormular()
	 */
	public function testAusFormular()
	{
		// TODO Auto-generated models_ClassGenerator_GeneratorTest->testAusFormular()
		$this->markTestIncomplete("ausFormular test not implemented");

		$this->models_ClassGenerator_Generator->ausFormular(/* parameters */);

	}

	/**
	 * Tests models_ClassGenerator_Generator->erzeugeKlasse()
	 */
	public function testErzeugeKlasse()
	{
		// TODO Auto-generated models_ClassGenerator_GeneratorTest->testErzeugeKlasse()
		$this->markTestIncomplete("erzeugeKlasse test not implemented");

		$this->models_ClassGenerator_Generator->erzeugeKlasse(/* parameters */);

	}

	/**
	 * Tests models_ClassGenerator_Generator->liefereKlasse()
	 */
	public function testLiefereKlasse()
	{
		// TODO Auto-generated models_ClassGenerator_GeneratorTest->testLiefereKlasse()
		$this->markTestIncomplete("liefereKlasse test not implemented");

		$this->models_ClassGenerator_Generator->liefereKlasse(/* parameters */);

	}

	/**
	 * Tests models_ClassGenerator_Generator->liefereErgebnis()
	 */
	public function testLiefereErgebnis()
	{
		// TODO Auto-generated models_ClassGenerator_GeneratorTest->testLiefereErgebnis()
		$this->markTestIncomplete("liefereErgebnis test not implemented");

		$this->models_ClassGenerator_Generator->liefereErgebnis(/* parameters */);

	}

}

