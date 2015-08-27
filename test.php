<?php


class A {
    function example() {
        $a = 2;
        echo $a;
        echo "I am A::example() and provide basic functionality.<br />\n";
        return $a;
        
    }
}

class B extends A {
    function example() {
        parent::example();
        echo "I am B::example() and provide additional functionality.<br />\n";
        $b = $a; 
        echo $b;
       
    }
}

$b = new B;

// This will call B::example(), which will in turn call A::example().
$b->example();
?>