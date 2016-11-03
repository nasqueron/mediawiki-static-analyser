
<?php
error_reporting(E_ALL);

require 'vendor/autoload.php';

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;

use PhpParser\PrettyPrinter;
$code = file_get_contents('test.php');
$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
global $count;
$count = 0;
class MyNodeVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node) {
    global $count;
    	//var_dump($node);
        if ($node instanceof Node\Expr\Array_) {
            //var_dump($node);
	    $existing_keys = array();
	    $existing_keys_const = array();
	    foreach ($node->items as $key => $item_value) {
	    	    $count ++;
		    //echo $count,"\n";
		    if ($count === 3560) {
		    //var_dump($item_value);
		    }
		    if ($item_value->key !== NULL) {
		       if ($item_value->key instanceof Node\Expr\ConstFetch ) {
		       //var_dump($item_value);
		       $k = join('\\', $item_value->key->name->parts);
			  //echo $k,"\n";
			  if(isset($existing_keys_const[$k])) {
		       echo 'duplicate ',$k,' line: ',$item_value->key->name->getAttribute("startLine")," (repetition of line: ",$existing_keys_const[$k], " )\n";
		       //var_dump($item_value);
		       }
		    
			$existing_keys_const[$k] = $item_value->key->name->getAttribute("startLine");
		       } else {
		       $k =$item_value->key->value;   
	    	       if(isset($existing_keys[$k])) {
		       echo 'duplicate ',$k,' line: ',$item_value->key->getAttribute("startLine")," (repetition of line: ",$existing_keys[$k],"\n";
		       //var_dump($item_value);
		       }
		    
			$existing_keys[$k] =$item_value->key->getAttribute("startLine") ;
			}
		}
	    }
        }
    }
}

try {
    $stmts = $parser->parse($code);
    $traverser     = new NodeTraverser;
    $traverser->addVisitor(new MyNodeVisitor);
    $traverser->traverse($stmts);
    $prettyPrinter = new PrettyPrinter\Standard;
    //var_dump($stmts);
    //echo($prettyPrinter->prettyPrintFile($stmts));
    
    // $stmts is an array of statement nodes
} catch (Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}
?>