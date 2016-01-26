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

}
