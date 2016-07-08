# PhpCommon Comparison

[![Build Status](https://travis-ci.org/marcospassos/phpcommon-comparison.svg?branch=master)](https://travis-ci.org/marcospassos/phpcommon-comparison)
[![Code Coverage](https://scrutinizer-ci.com/g/marcospassos/phpcommon-comparison/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/marcospassos/phpcommon-comparison/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/marcospassos/phpcommon-comparison/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/marcospassos/phpcommon-comparison/?branch=master)
[![StyleCI](https://styleci.io/repos/60445417/shield)](https://styleci.io/repos/60445417)
[![Latest Stable Version](https://poser.pugx.org/phpcommon/comparison/v/stable)](https://packagist.org/packages/phpcommon/comparison)
[![Dependency Status](https://www.versioneye.com/user/projects/576a0606fdabcd003c031888/badge.svg?style=flat)](https://www.versioneye.com/user/projects/576a0606fdabcd003c031888)

Latest release: [1.0.0-beta](https://packagist.org/packages/phpcommon/comparison#1.0.0)

PHP 5.4+ library to represent equivalence relations and strategies for hashing
and sorting values.

Equivalence relations are useful to establish a generalized way for comparing
values in respect to domain-specific requirements, as well as to represent
custom criteria for comparing values into bounded contexts, specially for use
in collections.

Complementary capabilities, such as hashing and sorting, are also covered by
this library, making it a valuable addition to your development tool belt. 

The API is extensively documented in the source code. In addition, an
[HTML version][link-apidoc] is also available for more convenient viewing in
browser.

# Installation

Use [Composer][link-composer] to install the package:

```
$ composer require phpcommon/comparison
```

# Relations

A relation is a mathematical tool for describing associations between elements
of sets. Relations are widely used in computer science, especially in databases
and scheduling applications.

Unlike most modern languages, PHP does not support [operator overloading],
historically avoided as a design choice. In other words, it is not possible to
override the default behaviour of native operators, such as equal, identical,
greater than, less than, etc. For example, Java provides the
[Comparable][java-comparable] interface, while Python provides some
[magic methods][python-magic-methods].

The importance of such concept become more evident in situations where the
notion of equivalence or ordering varies according to the subject of
comparison or to the context, as discussed in the following sections.

# Equivalence

In mathematics, an [equivalence relation] is a binary relation that is
_reflexive_, _symmetric_ and _transitive_. In the computing field, however,
there is another property that must be take into account: _consistency_.
Consistency means that a relation should not produce different results for the
same input.

A ubiquitous equivalence relation is the equality relation between elements of
any set. Other examples includes:

* _"Has the same birthday as"_ on the set of all people.
* _"Is similar to" or "congruent to"_ on the set of all triangles.
* _"Has the same absolute value"_ on the set of real numbers.

For the purpose of this library, an equivalence relation can be generic or
type-specific. Type-specific relations are defined by implementing either
`Equatable` or `Equivalence` interfaces, while generic equivalences must
implement the last one.

## Equatable Objects

The `Equatable` interface defines a generalized method that a class implements
to create a type-specific method for determining equality of instances.

To illustrate, considers a class `Money`, which aims to represent monetary
values. This class is a good candidate for implementing the `Equatable`
interface, because `Money` is a [Value Object], that is, the notion of
equality of those objects isn't based on identity. Instead, two instances of
`Money` are equal if they have the same values. Thus, while
`Money::USD(5) === Money::USD(5)` returns `false`,
`Money::USD(5)->equals(Money::USD(5))` returns `true`.

Here is the class previously mentioned:

```php
final class Money implements Equatable
{
    private $amount;
    private $currency;
    
    public function __construct($amount, $currency)
    {
        $this->amount = (int) $amount;
        $this->currency = (string) $currency;
    }
    
    public function equals(Equatable $other)
    {
        if (!$other instanceof self) {
            return false;
        }
        
        return $this->amount === $other->amount && $this->currency === $other->currency; 
    }
}
```

## Equivalence Relations

There are many cases, though, where having an non-standard, or _external_, way
for comparing two values become necessary. Perhaps, the most obvious use case
for those custom relations is for use with collections, but it is also useful
for providing those capabilities to scalar values or an existing class
that cannot provide it itself, because it belongs to a third-party package or
built into PHP.

Suppose you are developing a software to help hospitals to manage blood
donations. One of the requirements says that a nurse can not collect blood from
donors who have the same blood type. A relation for this scenario would look
like this:

```php
use PhpCommon\Comparison\Equivalence;

class BloodGroupEquivalence implements Equivalence
{
    public function equals(Equatable $other)
    {
        return get_class($other) === static::class;
    }

    public function equivalent($left, $right)
    {
        if (!$left instanceof Person) {
            UnexpectedTypeException::forType(Person::class, $left);
        }

        if (!$right instanceof Person) {
            return false;
        }

        return $left->getBloodType() === $right->getBloodType();
    }
}
```

This relation determines whether two people are of same blood group:

```php
$equivalence = new BloodGroupEquivalence();
$donors = new BloodDonors($equivalence);
$james = new Person('James', 'A');
$john = new Person('John', 'A');

// James and John are considered equivalent once they are of the same blood group
var_dump($equivalence->equivalent($james, $john)); // Outputs bool(true)

// Initially, none of them are present in the collection
var_dump($volunteers->contains($james)); // Outputs bool(false)
var_dump($volunteers->contains($john)); // Outputs bool(false) 

// Add James to the set of volunteers
$donors->add($james);

// Now, considering only the blood group of each donor for equality, both of
// them are considered present in the collection
$donors->contains($james); // Outputs bool(true)
$donors->contains($john); // Outputs bool(true)
```

Since `BloodGroupEquivalence` establishes an equivalence relation between
people based on their blood group, any attempt to add John to the collection
will be ignored, because James is already present and they are of the same
blood type.

It may look a bit complicated for a simple requirement at first, but in real
cases it can be used to compare the equivalence among compatible blood types,
in order to partition donors into groups.

### Built-in Equivalence Relations

This library provides some generic equivalence relations as part of the
standard library, as described below.

#### Identity Equivalence

_Compares two values for identity._

This relation is based on the identical operator. Most of cases, two values are
considered equivalent if they have the same type and value, but there are a few
exceptions:

* Two strings are equivalent if they have the same sequence of characters,
  same length and same characters in corresponding positions.
* Two numbers are equivalent if they are numerically equal
  (have the same number value).
  * Positive and negative zeros are equivalent to one another.
  * `NAN` is unequal to every other value, including itself.
  * Positive and negative infinities are equal only to themselves.
* Two boolean values are equivalent if both are true or both are false.
* Two distinct objects are never equivalent. An expression comparing objects is
  only true if the operands reference the same instance.
* Two arrays are equivalent if they they contain the equivalent entries
  according to this relation, in the same order. Empty arrays are equivalent to
  one another.
* Two resources are equivalent if they have the same unique resource number.
* Null is only equivalent to itself.

The following table summarizes how operands of the various types are compared:

| `$A \ $B` | NULL       | Boolean         | Integer         | Float           | String          | Resource        | Array           | Object          |
------------|------------|-----------------|-----------------|-----------------|-----------------|-----------------|-----------------|-----------------|
| NULL      | **`true`** | `false`         | `false`         | `false`         | `false`         | `false`         | `false`         | `false`         |
| Boolean   | `false`    | **`$A === $B`** | `false`         | `false`         | `false`         | `false`         | `false`         | `false`         | 
| Integer   | `false`    | `false`         | **`$A === $B`** | `false`         | `false`         | `false`         | `false`         | `false`         | 
| Float     | `false`    | `false`         | `false`         | **`$A === $B`** | `false`         | `false`         | `false`         | `false`         | 
| String    | `false`    | `false`         | `false`         | `false`         | **`$A === $B`** | `false`         | `false`         | `false`         |
| Resource  | `false`    | `false`         | `false`         | `false`         | `false`         | **`$A === $B`** | `false`         | `false`         | 
| Array     | `false`    | `false`         | `false`         | `false`         | `false`         | `false`         | **`$A === $B`** | `false`         |
| Object    | `false`    | `false`         | `false`         | `false`         | `false`         | `false`         | `false`         | **`$A === $B`** |

#### Value Equivalence

_Compares two values for equality._

The value equivalence behaves exactly as the identity equivalence, except that
it delegates comparison between `Equatable` objects to the objects being
compared. Additionally, external relations can be specified for comparing
values of a particular type. It is useful in cases where is desirable to
override the default behaviour for a specific type, but keep all the others.
It is also useful for defining a relation for objects of classes that belong
to third-party packages or built into PHP.

The following rules are used to determine whether two values are considered
equivalent:

* Two strings are equivalent if they have the same sequence of characters,
  same length, and same characters in corresponding positions.
* Two numbers are equivalent if they are numerically equal
  (have the same number value).
  * Positive and negative zeros are equivalent to one another.
  * `NAN` is unequal to every other value, including itself.
  * Positive and negative infinities are equal only to themselves.
* Two boolean values are equivalent if both are true or both are false.
* Two objects are equivalent if any of the following conditions hold:
  * The both objects are instances of `Equatable` and the expression
   `$left->equals($right)` is evaluated to `true`.
  * A specific equivalence relation is mapped to the type of the left-hand 
    value and the expression `$relation->equivalent($left, $right)` is
    evaluated to `true`.
  * Both values refer to the same instance of the same class in a particular
    namespace.
* Two arrays are equivalent if they they contain the equivalent entries
  according to this relation, in the same order. Empty arrays are equivalent to
  one another.
* Two resources are equivalent if they have the same unique resource number.
* Null is only equivalent to itself.

The following table summarizes how operands of the various types are compared:

| `$A \ $B` | NULL       | Boolean         | Integer         | Float           | String          | Resource        | Array            | Object          | Equatable            |
------------|------------|-----------------|-----------------|-----------------|-----------------|-----------------|------------------|-----------------|----------------------|
| NULL      | **`true`** | `false`         | `false`         | `false`         | `false`         | `false`         | `false`          | `false`         | `false`              | 
| Boolean   | `false`    | **`$A === $B`** | `false`         | `false`         | `false`         | `false`         | `false`          | `false`         | `false`              | 
| Integer   | `false`    | `false`         | **`$A === $B`** | `false`         | `false`         | `false`         | `false`          | `false`         | `false`              | 
| Float     | `false`    | `false`         | `false`         | **`$A === $B`** | `false`         | `false`         | `false`          | `false`         | `false`              | 
| String    | `false`    | `false`         | `false`         | `false`         | **`$A === $B`** | `false`         | `false`          | `false`         | `false`              |
| Resource  | `false`    | `false`         | `false`         | `false`         | `false`         | **`$A === $B`** | `false`          | `false`         | `false`              | 
| Array     | `false`    | `false`         | `false`         | `false`         | `false`         | `false`         | **`eq($A, $B)`** | `false`         | `false`              |
| Object    | `false`    | `false`         | `false`         | `false`         | `false`         | `false`         | `false`          | **`$A === $B`** | `false`              |
| Equatable | `false`    | `false`         | `false`         | `false`         | `false`         | `false`         | `false`          | `false`         | **`$A‑>equals($B)`** |

Where `eq()` denotes a function that compares each pair of corresponding
entries recursively, according to the rules described above.

This relation also provides a way to override the equivalence logic for a
particular class without the need to create a new relation. For example,
suppose you want to compare instances of `\DateTime` based on their values,
but keep the default behaviour for the other types. It can be accomplished by
specifying a custom relation to be used whenever an instance of `\DateTime`
is compared against another value:

```php
use PhpCommon\Comparison\Hasher\ValueHasher as ValueEquivalence;
use PhpCommon\Comparison\Hasher\DateTimeHasher as DateTimeEquivalence;
use DateTime;

$relation = new ValueEquivalence([
    DateTime::class => new DateTimeEquivalence()
]);

$date = '2017-01-01';
$timezone = new DateTimeZone('Pacific/Nauru');

$left = new DateTime($date, $timezone);
$right = new DateTime($date, $timezone);

// Outputs bool(true)
var_dump($relation->equivalent($left, $right));
```

#### Semantic Equivalence

_Compares two values for semantic equality._

A semantic equivalence is planned for future versions. It would allow the
comparison of values that look semantically similar - even if they are of
different types. It is similar to how loose comparison works in PHP, but under
more restrictive conditions, in such a way that properties of reflexivity,
symmetry and transitivity hold.

#### DateTime Value Equivalence

_Compares two `\DateTime` instances based on their date, time and time zone._

This relation considers two instances of `\DateTime` to be equivalent if they
have the same date, time and time zone:

```php
use PhpCommon\Comparison\Hasher\IdentityHasher as IdentityEquivalence;
use PhpCommon\Comparison\Hasher\DateTimeHasher as DateTimeEquivalence;
use DateTime;

$identity = new IdentityEquivalence();
$value = new DateTimeEquivalence();

$date = '2017-01-01';
$timezone = new DateTimeZone('Pacific/Nauru');

$left = new DateTime($date, $timezone);
$right = new DateTime($date, $timezone);

// Outputs bool(false)
var_dump($identity->equivalent($left, $right));

// Outputs bool(true)
var_dump($value->equivalent($left, $right));
```

# Hashing

In PHP, array keys can be only represented as numbers and strings. However,
there are several cases where storing complex types as keys is helpful.
Take as example classes that represent different kinds of numbers or strings,
such as GMP objects, Unicode strings, etc. It would be convenient to be able to
use such objects as array keys too.

To fill this gap, this library introduces the interfaces `Hashable` and
`Hasher`, which specify a protocol for providing hash codes for values. These
interfaces does not require implementors to provide perfect hashing functions.
That is, two values that are not equivalent may have the same hash code.
However, to determine whether two values with the same hash code are, in fact,
equal, the concepts of hashing and equivalence should be combined in a
complementary way. It explains why `Hasher` and `Hashable` extends
`Equivalence` and `Equatable` respectively.

> **A word of warning**

> A hash code is intended for efficient insertion and lookup in collections that
> are based on a hash table and for fast inequality checks. A hash code is not a
> permanent value. For this reason:

> * Do not serialize hash code values or store them in databases.
> * Do not use the hash code as the key to retrieve an object from a keyed
>   collection.
> * Do not send hash codes across application domains or processes. In some
>   cases, hash codes may be computed on a per-process or per-application
>   domain basis.
> * Do not use the hash code instead of a value returned by a cryptographic
>   hashing function if you need a cryptographically strong hash.
> * Do not test for equality of hash codes to determine whether two objects are
>   equal, once unequal values can have identical hash codes.

## Hashable

There are cases where it might be desirable to define a custom hashing logic
for a class to best fit your requirements. For example, suppose you have a
Point class to represent a 2D point:

```php
namespace PhpCommon\Comparison\Equatable;

final class Point implements Equatable
{
    private $x;
    private $y;
    
    public function __construct($x, $y)
    {
        $this->x = (int) $x;
        $this->y = (int) $y;
    }
    
    public function equals(Equatable $point)
    {
        if (!$point instanceof Point) {
            return false;
        }
        
        return $this->x === $point->x && $this->y === $point->y;
    }
}
```

A `Point` holds the x and y coordinates of a point. According to the definition
of the class, two points are considered equal if they have the same
coordinates. However, if you intend to store instances of `Point` in an
hash-based map, for example, because you want to associate coordinates to
labels, then you must ensure that your class produces hash codes that are
_coherent_ with the logic used for determining when two points are considered
equal:

```php
namespace PhpCommon\Comparison\Equatable;

final class Point implements Hashable
{
    private $x;
    private $y;
    
    public function __construct($x, $y)
    {
        $this->x = (int) $x;
        $this->y = (int) $y;
    }
    
    public function equals(Equatable $point)
    {
        if (!$point instanceof Point) {
            return false;
        }
        
        return $this->x === $point->x && $this->y === $point->y;
    }

    public function getHash()
    {
        return 37 * (31 + $this->$x) + $this->$x;
    }
}
```

In that way, the `getHash()` method works in accordance to the `equals()`
method, although the hashing algorithm may not be ideal. The implementation
of efficient algorithms for hash code generation is beyond the scope of this
guide. However, it is recommended to use a fast algorithm that produces
reasonably different results for unequal values, and shift the heavy comparison
logic to `Equatable::equals()`.

> Notice that hashable objects should either be immutable, or you need to
> exercise discipline in not changing them after they have been used in a
> hash-based structures.

## Hasher

A Hasher provides hashing functionality for primitive types and objects of
classes that do not implement `Hashable`. 

The method `hash()` introduced by this interface is intended to provide a
means for performing fast _inequivalence_ checks and efficient insertion and
lookup in hash-based data structures. This method is always _coherent_ with
`equivalent()`, which means that for any references `$x` and `$y`, if
`equivalent($x, $y)`, then `hash($x) === hash($y)`. However, if
`equivalence($x, $y)` evaluates to `false`, `hash($x) === hash($y)` may
still be true. Hence why the `hash()` method is suitable for _inequivalence_
checks, but not _equivalence_ checks.

All implementations of `Equivalence` included in this library also provide
hashing functionality. More information about how values are hashed can be
found in the documentation of the respective implementation.

# Sorting

Following the same logic of the concepts previously discussed, `Comparable` and
`Comparator` are interfaces to provide _natural_ and custom sorting strategies,
respectively. Both interfaces specify a [total order] relation, a relation
that is _reflexive_, _antisymmetric_ and _transitive_.

## Comparable

This interface imposes a total ordering on the objects of each class that
implements it. This ordering is referred to as the _natural ordering_ of the
class, and the method `Comparable::compareTo()` is referred to as its
_natural comparison method_.

The following example shows how a class can define the natural order of its
instances:

```php
use PhpCommon\Comparison\UnexpectedTypeException;

final class BigInteger implements Comparable
{
    private $value;

    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    public function compareTo(Comparable $other)
    {
        if (!$other instanceof self) {
            throw UnexpectedTypeException::forType(BigInteger::class, $other);
        }

        return bccomp($this->value, $other->value);
    }
}
```

## Comparator

The purpose of a `Comparator` is to allow you to define one or more comparison
strategies that are not the natural comparison strategy for a class. Ideally, a
`Comparator` must be implemented by a class different from the one it defines
the comparison strategy for. If you want to define a natural comparison
strategy for a class, you can implement `Comparable` instead.

Comparators can be passed to a sort method of a collection to allow precise
control over its sort order. It can also be used to control the order of
certain data structures, such as sorted sets or sorted maps. For example,
consider the following comparator that orders strings according to
their length:

```php
use PhpCommon\Comparison\Comparator;

class StringLengthComparator implements Comparator
{ 
    public function compare($left, $right)
    {
        return strlen($left) <=> strlen($right);
    }
}

$comparator = new StringLengthComparator();

// Outputs int(-1)
var_dump($comparator->compare('ab', 'a'));
```

This implementation represents one of many possible ways of sorting strings.
Other strategies includes sorting alphabetically, lexicographically, etc.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.


## Testing

``` bash
$ composer test
```

Check out the [Test Documentation][link-testsdoc] for more details.

## Contributing

Contributions to the package are always welcome!

* Report any bugs or issues you find on the [issue tracker][link-issue-tracker].
* You can grab the source code at the package's
[Git repository][link-repository].

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for
details.

## Security

If you discover any security related issues, please email
marcos@marcospassos.com instead of using the issue tracker.

## Credits

* [Marcos Passos][link-author]
- [All Contributors][link-contributors]

## License

All contents of this package are licensed under the [MIT license](LICENSE).

[operator overloading]: https://en.wikipedia.org/wiki/Operator_overloading
[java-comparable]: https://docs.oracle.com/javase/7/docs/api/java/lang/Comparable.html
[python-magic-methods]: https://docs.python.org/3/reference/datamodel.html#object.__lt__
[equivalence relation]: https://en.wikipedia.org/wiki/Equivalence_relation
[value object]: https://en.wikipedia.org/wiki/Value_object
[total order]: https://en.wikipedia.org/wiki/Total_order
[link-apidoc]: http://marcospassos.github.io/phpcommon-comparison/docs/api
[link-testsdoc]: http://marcospassos.github.io/phpcommon-comparison/docs/test
[link-composer]: https://getcomposer.org
[link-author]: http://github.com/marcospassos
[link-contributors]: https://github.com/marcospassos/phpcommon-comparison/graphs/contributors
[link-issue-tracker]: https://github.com/marcospassos/phpcommon-comparison/issues
[link-repository]: https://github.com/marcospassos/phpcommon-comparison
