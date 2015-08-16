# MultiCombinator
Generates all combination of multiple arrays.
Can be used as an iterator using methods like next(), rewind().

Given two arrays, the following code:

$lists = [
  'a' => [ 'A', 'B', 'C' ],
  'b' => [ 'A', 'B', 'C' ]
]

$c = new MultiCombinator( $lists );
print_r( $c->getAll() );


prints an array containing the following combinations:
A A
A B
A C

B A
B B
B C

C A
C B
C C
