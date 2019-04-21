<?php

/**
 * IndexController test case.
 */
namespace Tests;
use Controllers\IndexController;
use Framework\Box;

class IndexControllerTest extends BaseTestCase
{

    /**
     *
     * @var \Controllers\IndexController
     */
    private $indexController;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp (): void
    {
        $this->box = new Box();
        parent::setUp();
        $this->indexController = new IndexController($this->box);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown (): void
    {
        // TODO Auto-generated IndexControllerTest::tearDown()
        $this->indexController = null;

        parent::tearDown();
    }

    /**
     * Tests IndexController->indexAction()
     */
    public function testIndexAction ()
    {
        ob_start();
        $this->indexController->indexAction();

        $html = ob_get_clean();
        $this->assertStringContainsString('</html>', $html);

    }

    /**
     * Tests IndexController->subpageAction()
     */
    public function testSubpageAction ()
    {
        ob_start();
        $this->indexController->subpageAction();

        $html = ob_get_clean();
        $this->assertStringContainsString('</html>', $html);
    }
}
