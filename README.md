# Resolver Service

The Resolver Service is an abstraction layer of PSR-11 container.

## Table of Contents

- [Getting started](#getting-started)
	- [Requirements](#requirements)
	- [Highlights](#highlights)
- [Documentation](#documentation)
    - [Implementations](#implementations)
    - [Resolver Factory](#resolver-factory)
    - [Resolver](#resolver)
        - [PSR-11](#psr-11)
        - [Autowiring](#autowiring)
        - [Definitions](#definitions)
        - [Make](#make)
        - [Call](#call)
        - [On](#on)
            - [Replace objects](#replace-objects)
            - [Modify objects](#modify-objects)
            - [Construct objects](#construct-objects)
            - [Call Methods](#call-methods)
            - [Using Once](#using-once)
            - [Using Prototype](#using-prototype)
            - [Using Instanceof](#using-instanceof)
            - [Using Priority](#using-priority)
        - [Rule](#rule)
        - [Container](#container)
- [Credits](#credits)
___

# Getting started

Add the latest version of the resolver service project running this command.

```
composer require tobento/service-resolver
```

## Requirements

- PHP 8.0 or greater

## Highlights

- Framework-agnostic, will work with any project
- Decoupled design
- Autowiring

# Documentation

## Implementations

Currently, there are the following implementations:

- [service-resolver-container](https://github.com/tobento-ch/service-resolver-container)
- [service-resolver-league](https://github.com/tobento-ch/service-resolver-league)

## Resolver Factory

```php
use Tobento\Service\ResolverContainer\ResolverFactory;
use Tobento\Service\Resolver\ResolverFactoryInterface;
use Tobento\Service\Resolver\ResolverInterface;

$resolverFactory = new ResolverFactory();

var_dump($resolverFactory instanceof ResolverFactoryInterface);
// bool(true)

// create resolver
$resolver = $resolverFactory->createResolver();

var_dump($resolver instanceof ResolverInterface);
// bool(true)
```

## Resolver

### PSR-11

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class Foo {}

$resolver = (new ResolverFactory())->createResolver();

var_dump($resolver->has(Bar::class));
// bool(false)

var_dump($resolver->get(Foo::class));
// object(Foo)#5 (0) { }
```

### Autowiring

The resolver resolves any dependencies by autowiring, except build-in parameters needs a [definition](#definitions) to be resolved.

On union types parameter, the first resolvable parameter gets used if not set by definiton.

### Definitions

**By providing the resolved object:**

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class Foo
{
    public function __construct(
        protected string $name
    ) {} 
}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(Foo::class, new Foo('value'));

var_dump($resolver->get(Foo::class));
// object(Foo)#3 (1) { ["name":protected]=> string(5) "value" }
```

**By defining the missing parameters:**

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class Foo
{
    public function __construct(
        protected string $name
    ) {}
}

$resolver = (new ResolverFactory())->createResolver();

// By the construct method:
$resolver->set(Foo::class)->construct('value');

// By the with method using parameter name:
$resolver->set(Foo::class)->with(['name' => 'value']);

// By the with method using parameter position:
$resolver->set(Foo::class)->with([0 => 'value']);

var_dump($resolver->get(Foo::class));
// object(Foo)#9 (1) { ["name":protected]=> string(5) "value" }
```

**By using a closure:**

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class Foo
{
    public function __construct(
        protected string $name
    ) {}
}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(Foo::class, function($container) {
    return new Foo('value');
});

var_dump($resolver->get(Foo::class));
// object(Foo)#8 (1) { ["name":protected]=> string(5) "value" }
```

**You might configure which implementation to use:**

```php
$resolver->set(BarInterface::class, Bar::class);
```

**Defining method calls:** You will need only to define build-in parameters as others get autowired if you want to.

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class Foo
{
    protected array $called = [];
    
    public function index(Bar $bar, string $name)
    {
        $this->called[] = [$bar, $name];
    } 
}

class Bar {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(Foo::class)->callMethod('index', ['name' => 'value']);

$resolver->set(Foo::class)->callMethod('index', [1 => 'value']);

$foo = $resolver->get(Foo::class);
```

**Prototype Definition:**

You might declare the defintion as prototype, meaning returning always a new instance.

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class Foo {}
class Bar {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(Foo::class)->prototype();

$resolver->set(Bar::class, function() {
    return new Bar();
})->prototype();

var_dump($resolver->get(Foo::class) === $resolver->get(Foo::class));
// bool(false)

var_dump($resolver->get(Bar::class) === $resolver->get(Bar::class));
// bool(false)
```

### Make

The make() method works like get() except it will resolve the entry every time it is called.

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class Foo
{
    public function __construct(
        private Bar $bar,
        private string $name
    ) {} 
}

class Bar {}

$resolver = (new ResolverFactory())->createResolver();

$foo = $resolver->make(Foo::class, ['name' => 'value']);
```

### Call

For more detail visit: [service-autowire#call](https://github.com/tobento-ch/service-autowire#call)

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class Foo
{
    public function index(Bar $bar, string $name): string
    {
        return $name;
    } 
}

class Bar {}

$resolver = (new ResolverFactory())->createResolver();

$name = $resolver->call([Foo::class, 'index'], ['name' => 'value']);

var_dump($name);
// string(5) "value"
```

### On

With the **on** method, you may replace, modify or construct objects.

#### Replace objects

**Replace object**

You may replace the resolved object by simply declare a class:

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class AdminUser {}
class GuestUser {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->on(AdminUser::class, GuestUser::class);

$user = $resolver->get(AdminUser::class);

var_dump($user);
// object(GuestUser)#9 (0) { }
```

**Replace object by using a callable**

You may replace the resolved object by using a callable. The first argument will always be the resolved object, but you can typehint any other object you may need next.

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class AdminUser {}
class GuestUser {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->on(AdminUser::class, function($user) {
    return new GuestUser();
});

// with typehint:
$resolver->on(AdminUser::class, function($user, GuestUser $guest) {
    return $guest;
});
         
$user = $resolver->get(AdminUser::class);

var_dump($user);
// object(GuestUser)#17 (0) { }
```

#### Modify objects

**Modify object by using a callable**

You may modify the resolved object by using a callable. The first argument will always be the resolved object, but you can typehint any other object you may need next.

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class AdminUser {}
class GuestUser {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->on(AdminUser::class, function($user) {
    // modify $user
});
         
$user = $resolver->get(AdminUser::class);

var_dump($user);
// object(AdminUser)#9 (0) { }
```

#### Construct objects

**Construct object by providing an array**

You may wish to inject different implementations into each class or inject any primitive values.

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

interface UserInterface {}
class GuestUser implements UserInterface {}
class AdminUser implements UserInterface {}

class ServiceFoo {
    public function __construct(
        protected UserInterface $user
    ) {}
}

class ServiceBar {
    public function __construct(
        protected UserInterface $user
    ) {}
}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(UserInterface::class, GuestUser::class);

$resolver->on(ServiceFoo::class, ['user' => AdminUser::class]);

var_dump($resolver->get(ServiceFoo::class));
// object(ServiceFoo)#14 (1) { ["user":protected]=> object(AdminUser)#11 (0) { } }

var_dump($resolver->get(ServiceBar::class));
// object(ServiceBar)#9 (1) { ["user":protected]=> object(GuestUser)#13 (0) { } }
```

**Construct object by using a callable returning an array**

You may wish to inject different implementations into each class or inject any primitive values by using a callable returning the resolve values. The first argument of the callable will always be the resolved object, but you can typehint any other object you may need next.

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

interface UserInterface {}
class GuestUser implements UserInterface {}
class AdminUser implements UserInterface {}

class ServiceFoo {
    public function __construct(
        protected UserInterface $user
    ) {}
}

class ServiceBar {
    public function __construct(
        protected UserInterface $user
    ) {}
}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(UserInterface::class, GuestUser::class);

$resolver->on(ServiceFoo::class, function($service, AdminUser $user) {
    return ['user' => $user];
});

var_dump($resolver->get(ServiceFoo::class));
// object(ServiceFoo)#12 (1) { ["user":protected]=> object(AdminUser)#17 (0) { } }

var_dump($resolver->get(ServiceBar::class));
// object(ServiceBar)#11 (1) { ["user":protected]=> object(GuestUser)#14 (0) { } }
```

#### Call Methods

You may want to call methods after an object is resolved:

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class FooService {}
class AdminUser {
    protected array $called = [];
    
    public function set(FooService $service, string $value)
    {
        $this->called[] = [$service, $value];
    }
    
    public function getCalled(): array
    {
        return $this->called;
    }
}

$resolver = (new ResolverFactory())->createResolver();

$resolver->on(AdminUser::class)
         ->callMethod('set', ['value' => 'foo']);

$user = $resolver->get(AdminUser::class);

var_dump($user->getCalled());
// array(1) { [0]=> array(2) { [0]=> object(FooService)#15 (0) { } [1]=> string(3) "foo" } }
```

**Declare as trait**

You may calling methods when a class uses a trait:

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

trait SomeMethods {
    protected array $called = [];
    
    public function set(string $value)
    {
        $this->called[] = $value;
    }
    
    public function getCalled(): array
    {
        return $this->called;
    }    
}

class AdminUser {
    use SomeMethods;
}

$resolver = (new ResolverFactory())->createResolver();

$resolver->on(SomeMethods::class)
         ->trait()
         ->once(false)
         ->callMethod('set', ['value' => 'foo']);

$user = $resolver->get(AdminUser::class);

var_dump($user->getCalled());
// array(1) { [0]=> string(3) "foo" }
```

#### Using Once

The **on** method is handled once as default. You may use the **once** method as to be always handled:

```php
class AdminUser {}
class GuestUser {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(AdminUser::class)->prototype();

$resolver->on(AdminUser::class, GuestUser::class);
         ->once(false);

var_dump($resolver->get(AdminUser::class));
// object(GuestUser)#10 (0) { }

var_dump($resolver->get(AdminUser::class));
// object(GuestUser)#10 (0) { }
```

#### Using Prototype

You may using the **prototype** method returning always a new instance:

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class AdminUser {}
class GuestUser {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(AdminUser::class)->prototype();

$resolver->on(AdminUser::class, GuestUser::class)
         ->once(false)
         ->prototype();

var_dump(
    $resolver->get(AdminUser::class)
    === $resolver->get(AdminUser::class)
);
// bool(false)
```

#### Using Instanceof

You may modify or replace objects when the object belongs to a specific class by using the **instanceof** method:

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

interface UserInterface {}
class User implements UserInterface {}
class GuestUser extends User {}
class AdminUser extends User {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->set(UserInterface::class, GuestUser::class);

$resolver->on(User::class, function($user) {
    return new AdminUser();
})->instanceof();

var_dump($resolver->get(UserInterface::class));
// object(AdminUser)#12 (0) { }
```

#### Using Priority

You may use the **priority** method to handle the execution order. The default priority is 1000, highest gets handled first:

```php
use Tobento\Service\ResolverContainer\ResolverFactory;

class AdminUser {}
class GuestUser {}
class EditorUser {}

$resolver = (new ResolverFactory())->createResolver();

$resolver->on(AdminUser::class, EditorUser::class)
         ->priority(500);

$resolver->on(AdminUser::class, GuestUser::class)
         ->priority(1000);

var_dump($resolver->get(AdminUser::class));
// object(EditorUser)#8 (0) { }
```

### Rule

You may add a rule by using the **rule** method:

```php
use Tobento\Service\ResolverContainer\ResolverFactory;
use Tobento\Service\Resolver\RuleInterface;
use Tobento\Service\Resolver\OnRule;

class AdminUser {}
class GuestUser {}

$resolver = (new ResolverFactory())->createResolver();

$rule = new OnRule(AdminUser::class, GuestUser::class);

var_dump($rule instanceof RuleInterface);
// bool(true)

$resolver->rule($rule);

$user = $resolver->get(AdminUser::class);

var_dump($user);
// object(GuestUser)#9 (0) { }
```

### Container

```php
use Tobento\Service\ResolverContainer\ResolverFactory;
use Psr\Container\ContainerInterface;

$resolver = (new ResolverFactory())->createResolver();

var_dump($resolver->container() instanceof ContainerInterface);
// bool(true)
```

# Credits

- [Tobias Strub](https://www.tobento.ch)
- [All Contributors](../../contributors)