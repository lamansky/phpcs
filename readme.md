# Lamansky PHP Code Standard

Enforces a set of coding style rules on your PHP project using [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer).

## Highlights

This standard is a superset of [PSR-1](https://www.php-fig.org/psr/psr-1/) and adds, among other things, the following rules:

* Must indent with 4 spaces, not tabs.
* Opening braces must be on the same line: `class MyClass {`.
* No assigning variables in `if` statements (e.g. `if ($var = get()) {`).
* No “Yoda” conditions (e.g. `if (true === $var) {`).
* No inline control structures (curly brackets always required).
* No group `use` declarations.
* All methods and class constants must declare visibility.
* All functions must declare a return type.
* Can only declare arrays with `[]`, not `array()`.
* Multi-line array declarations must have trailing commas.
* Single-quote strings must be used where possible.

## Installation and Usage

You don’t need to install PHP_CodeSniffer separately. It will be installed when you install this package.

With [Composer](http://getcomposer.org) installed on your computer and initialized for your PHP project, run this command from the terminal in your project’s root directory:

```bash
composer require --dev lamansky/phpcs
```

Then, in your project’s directory, add a file called `phpcs.xml` that contains the following XML code. (Replace `YourProject` with the name of your project.)

```xml
<?xml version="1.0"?>
<ruleset name="YourProject">
  <rule ref="Lamansky"/>
</ruleset>
```

If you use the [Atom text editor](https://atom.io/), install the [linter](https://atom.io/packages/linter) and [linter-phpcs](https://atom.io/packages/linter-phpcs) packages to get on-the-fly error notices as you code.

Alternatively, you can check for coding errors by running  PHP_CodeSniffer from the terminal in your project’s directory:

```bash
./vendor/bin/phpcs . -pv --ignore=vendor --extensions=php
```

### Customizing the Rules

This ruleset contains a few custom rules, but it mostly imports rules from its dependencies.

If you wish to exclude a rule, you can find its `ref` ID in the `Lamansky/ruleset.xml` file and then exclude it in your project’s `phpcs.xml` file, by putting the ID into the `name` attribute of an `exclude` tag like this:

```xml
<?xml version="1.0"?>
<ruleset name="YourProject">
  <rule ref="Lamansky">
    <exclude name="Lamansky.Functions.DeclarationSpace"/>
  </rule>
</ruleset>
```

Some of the rules have settings that you can change to tweak how the rule is evaluated. These settings are declared as public variables in the PHP classes that control the various rules. Here’s an example of setting a value for one of these settings:

```xml
<?xml version="1.0"?>
<ruleset name="YourProject">
  <rule ref="Lamansky"/>
  <rule ref="Lamansky.Functions.ReturnType">
    <properties>
      <property name="requireNativeDeclaration" value="true"/>
    </properties>
  </rule>
</ruleset>
```

## Custom Rules

Most of the rules are imported from other projects, but there are a few rules that are unique to this project.

### Lamansky.Functions.DeclarationSpace

Enforces a space between the function name and parameters list when declaring a function: `function abc (...)`. This is a visual cue that helps distinguish a function declaration from a function invocation when skimming code.

### Lamansky.Functions.ReturnType

This rule performs the following three functions:

* Requires every function to either declare a [return type](https://www.php.net/manual/en/functions.returning-values.php#functions.returning-values.type-declaration) (e.g. `function abc (...) : string`) or have a docblock `@return` tag.
* Alerts you if a function returns `null` but doesn’t have a nullable return type (e.g. `?string`).
* Alerts you if a function returns a value despite having a `void` return type.

If you wish to allow functions to omit a return type declaration, but wish to retain the other functionality, set the `allowUndeclaredReturnType` property to `true`.

Docblock `@return` tags are supported for cases in which PHP7’s return type declaration support isn’t sufficiently flexible. If your project only supports PHP8 or above, then you can use  [union types](https://wiki.php.net/rfc/union_types_v2) and the [`mixed` keyword](https://wiki.php.net/rfc/mixed_type_v2) and probably have no need of the docblock fallback. You can disable it by setting the `requireNativeDeclaration` property to `true`.

## Related

* [eslint-config-lamansky](https://github.com/lamansky/eslint-config-lamansky): A code standard for JavaScript.
