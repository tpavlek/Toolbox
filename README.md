Depotwarehouse.net Toolbox
===========================

[![Build Status](https://travis-ci.org/tpavlek/Toolbox.svg?branch=master)](https://travis-ci.org/tpavlek/Toolbox) 
[![Coverage Status](https://coveralls.io/repos/tpavlek/Toolbox/badge.png?branch=master)](https://coveralls.io/r/tpavlek/Toolbox?branch=master)

This is a standard library of tools that abstracts common work needed to be done across all projects.

Database Management
--------------------

There are a couple of classes that make working with databases easier in Laravel projects. The repository pattern
allows for greater testability and easier to understand controllers. In your projects, simply create repositories that
extend from `ActiveRepositoryAbstract`. For typehints you can use the contract `ActiveRepository`.

The active repository requires a `Validator` class to validate data processed within - this class is passed in via the constructor.
You should either create your own validator implementing the `Validator` contract, or you can typehint the NullValidator
to perform no validation on the model whatsoever.

```php

class MyActiveRepository extends ActiveRepositoryAbstract 
{
    public function __construct(\Illuminate\Database\Eloquent\Model $model, \Depotwarehouse\Toolbox\Validation\NullValidator $validator)
    {
        $this->model = $model;
        $this->validator = $validator;
    }
}
```


