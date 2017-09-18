<?php
/**
 * User: Victor Häggqvist
 * Date: 3/4/15
 * Time: 3:12 AM
 */

use Snilius\Twig\SortByFieldExtension;

require_once __DIR__.'/../vendor/autoload.php';

class SortByFieldExtensionTest extends PHPUnit_Framework_TestCase {

    public function testExtensionLoad() {
        $loader = new Twig_Loader_Array(array('foo' => ''));
        $twig = new Twig_Environment($loader);
        $twig->addExtension(new SortByFieldExtension());
        $this->addToAssertionCount(1);
        $twig->render('foo');
    }

    public function testSortArray() {
        $base = array(
            array(
                "name" => "Redmine",
                "desc" => "Issues Tracker",
                "url" => "http://www.redmine.org/",
                "oss" => "GPL",
                "cost" => 0
            ),
            array(
                "name" => "GitLab",
                "desc" => "Version Control",
                "url" => "https://about.gitlab.com/",
                "oss" => "GPL",
                "cost" => 1,
            ),
            array(
                "name" => "Jenkins",
                "desc" => "Continous Integration",
                "url" => "http://jenkins-ci.org/",
                "oss" => "MIT",
                "cost" => 0,
            ),
            array(
                "name" => "Piwik",
                "desc" => "Web Analytics",
                "url" => "http://piwik.org/",
                "oss" => "GPL",
                "cost" => 1
            )
        );

        $fact = array('GitLab', 'Jenkins', 'Piwik', 'Redmine');

        $filter = new SortByFieldExtension();
        $sorted = $filter->sortByFieldFilter($base, 'name');

        for ($i = 0; $i < count($fact); $i++) {
            $this->assertEquals($fact[$i], $sorted[$i]['name']);
        }
    }

    public function testSortArrayWithKeys() {
        $base = array(
            "a" => array(
                "name" => "Redmine",
                "desc" => "Issues Tracker",
                "url" => "http://www.redmine.org/",
                "oss" => "GPL",
                "cost" => 0
            ),
            "b" => array(
                "name" => "GitLab",
                "desc" => "Version Control",
                "url" => "https://about.gitlab.com/",
                "oss" => "GPL",
                "cost" => 1,
            ),
            "c" => array(
                "name" => "Jenkins",
                "desc" => "Continous Integration",
                "url" => "http://jenkins-ci.org/",
                "oss" => "MIT",
                "cost" => 0,
            ),
            "d" => array(
                "name" => "Piwik",
                "desc" => "Web Analytics",
                "url" => "http://piwik.org/",
                "oss" => "GPL",
                "cost" => 1
            )
        );

        $fact = array('GitLab', 'Jenkins', 'Piwik', 'Redmine');

        $filter = new SortByFieldExtension();
        $sorted = $filter->sortByFieldFilter($base, 'name');

        for ($i = 0; $i < count($fact); $i++) {
            $this->assertEquals($fact[$i], $sorted[$i]['name']);
        }
    }

    public function testSortObjects() {
        $base = array();
        $ob1 = new Foo();
        $ob1->name = "Redmine";
        $base[] = $ob1;

        $ob2 = new Foo();
        $ob2->name = "GitLab";
        $base[] = $ob2;

        $ob3 = new Foo();
        $ob3->name = "Jenkins";
        $base[] = $ob3;

        $ob4 = new Foo();
        $ob4->name = "Jenkins";
        $base[] = $ob4;

        $fact = array('GitLab', 'Jenkins', 'Jenkins', 'Redmine');

        $filter = new SortByFieldExtension();
        $sorted = $filter->sortByFieldFilter($base, 'name');

        for ($i = 0; $i < count($fact); $i++) {
            $this->assertEquals($fact[$i], $sorted[$i]->name);
        }
    }

    public function testSortObjectsMagicProperty() {
        $base = array();
        $ob1 = new Foo();
        $ob1->magicName = "Redmine";
        $base[] = $ob1;

        $ob2 = new Foo();
        $ob2->magicName = "GitLab";
        $base[] = $ob2;

        $ob3 = new Foo();
        $ob3->magicName = "Jenkins";
        $base[] = $ob3;

        $ob4 = new Foo();
        $ob4->magicName = "Jenkins";
        $base[] = $ob4;

        $fact = array('GitLab', 'Jenkins', 'Jenkins', 'Redmine');

        $filter = new SortByFieldExtension();
        $sorted = $filter->sortByFieldFilter($base, 'magicName');

        for ($i = 0; $i < count($fact); $i++) {
            $this->assertEquals($fact[$i], $sorted[$i]->magicName);
        }
    }

    public function testSortDoctrineCollection() {
        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        $ob1 = new Foo();
        $ob1->name = "Redmine";
        $collection->add($ob1);

        $ob2 = new Foo();
        $ob2->name = "GitLab";
        $collection->add($ob2);

        $ob3 = new Foo();
        $ob3->name = "Jenkins";
        $collection->add($ob3);

        $ob4 = new Foo();
        $ob4->name = "Piwik";
        $collection->add($ob4);
        $fact = array('GitLab', 'Jenkins', 'Piwik', 'Redmine');

        $filter = new SortByFieldExtension();
        $sorted = $filter->sortByFieldFilter($collection, 'name');

        for ($i = 0; $i < count($fact); $i++) {
            $this->assertEquals($fact[$i], $sorted[$i]->name);
        }
    }

    public function testNonArrayBase() {
        $filter = new SortByFieldExtension();
        $this->setExpectedException('InvalidArgumentException');
        $filter->sortByFieldFilter(1, '');
    }

    public function testInvalidField() {
        $filter = new SortByFieldExtension();
        $this->setExpectedException('Exception');
        $filter->sortByFieldFilter(array(1,2,3), null);
    }

    public function testEmptyArray() {
        $filter = new SortByFieldExtension();
        $unTouchedArray = $filter->sortByFieldFilter(array());
        $this->assertEquals(array(), $unTouchedArray);
    }

    public function testUnknownField() {
        $filter = new SortByFieldExtension();
        $this->setExpectedException('Exception');
        $filter->sortByFieldFilter(array(new Foo()), 'bar');
    }

    public function testSortObjectObjectProperty()
    {
        /* @var Foo[] $base*/
        $base = [];

        $base[] = new Foo('2',new Foo('Redmine'));
        $base[] = new Foo('4',new Foo('Jenkins'));
        $base[] = new Foo('3',new Foo('GitLab'));
        $base[] = new Foo('1',new Foo('Jenkins'));

        $fact = array('GitLab', 'Jenkins', 'Jenkins', 'Redmine');

        $filter = new SortByFieldExtension();
        /* @var Foo[] $sorted */
        $sorted = $filter->sortByFieldFilter($base, 'object.name');

        for ($i = 0; $i < count($fact); $i++) {
            $this->assertEquals($fact[$i], $sorted[$i]->getObject()->name);
        }

    }

    public function testSortObjectObjectObjectProperty()
    {
        /* @var Foo[] $base */
        $base = [];

        $base[] = new Foo('3',new Foo('3-3',new Foo('Redmine')));
        $base[] = new Foo('4',new Foo('4-4',new Foo('Jenkins')));
        $base[] = new Foo('2',new Foo('2-2',new Foo('GitLab')));
        $base[] = new Foo('1',new Foo('1-1',new Foo('Jenkins')));

        $fact = array('GitLab', 'Jenkins', 'Jenkins', 'Redmine');

        $filter = new SortByFieldExtension();
        /* @var Foo[] $sorted */
        $sorted = $filter->sortByFieldFilter($base, 'object.object.name');

        for ($i = 0; $i < count($fact); $i++) {
            $this->assertEquals($fact[$i], $sorted[$i]->getObject()->getObject()->name);
        }
    }

    public function testUnknownFieldSortObjectObjectProperty()
    {
        /* @var Foo[] $base */
        $base = [];

        $base[] = new Foo('2',new Foo('Redmine'));
        $base[] = new Foo('4',new Foo('Jenkins'));
        $base[] = new Foo('3',new Foo('GitLab'));
        $base[] = new Foo('1',new Foo('Jenkins'));

        $filter = new SortByFieldExtension();
        $this->setExpectedException('Exception');
        $filter->sortByFieldFilter($base, 'object.unknownField');
    }


    public function testSortArrayArrayArrayProperty()
    {
        /* @var array $base */
        $base = [
            ['param' => ['value' => 'Redmine']],
            ['param' => ['value' => 'Jenkins']],
            ['param' => ['value' => 'GitLab']],
            ['param' => ['value' => 'Jenkins']],
        ];

        $fact = array('GitLab', 'Jenkins', 'Jenkins', 'Redmine');

        $filter = new SortByFieldExtension();
        $sorted = $filter->sortByFieldFilter($base, 'param.value');

        for ($i = 0; $i < count($fact); $i++) {
            $this->assertEquals($fact[$i], $sorted[$i]['param']['value']);
        }
    }

    public function testUnknownKeySortArrayArrayArrayProperty()
    {
        /* @var array $base */
        $base = [
            ['param' => ['value' => 'Redmine']],
            ['param' => ['value' => 'Jenkins']],
            ['param' => ['value' => 'GitLab']],
            ['param' => ['value' => 'Jenkins']],
        ];

        $filter = new SortByFieldExtension();
        $this->setExpectedException('Exception');
        $filter->sortByFieldFilter($base, 'param.UnknownKey');
    }
}
