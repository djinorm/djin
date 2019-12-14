# DjinORM [![Build Status](https://travis-ci.org/djinorm/djin.svg?branch=master)](https://travis-ci.org/djinorm/djin)

Легковесная ORM, нацеленная на работу с любым типом баз данных, будь то реляционные, документ-ориентированные, колоночные
или key-value хранилища. Вы полностью управляете тем, как что и куда вы хотите сохранять и извлекать. Все что для этого
нужно - создать репозитории для моделей

## Установка:
```bash
composer require djinorm/djin
```

## Преамбула

DjinORM состоит из набора компонентов и интерфейсов, взаимодейтвие которых позволяет инкапсулировать логику ее работы

## Модель
Модель - это объект, который мы хотим сохранять в БД, отдельная, полноценная сущность, которая может иметь в себе любые
вложенные объекты и массивы. Моделью может быть абсолютно любой класс, который реализует интерфейс 
[\DjinORM\Djin\Model\ModelInterface](src/Model/ModelInterface.php). Каждая модель должна иметь свой уникальный Id, благодаря
которому осуществляется связывание моделей друг с другом.

## Id
Id - объект, который содержит в себе уникальный идентификатор модели (Id - всегда строка, даже если вы явно передадите int). 
Именно объект [\DjinORM\Djin\Id\Id](src/Id/Id.php) должна возвращать каждая модель, и именно благодаря нему осуществляется 
связывание моделей друг с другом. В PHP все объекты [передаются по ссылке](https://www.php.net/manual/ru/language.oop5.references.php)  
благодаря чему мы можем присвоить Id какой-либо модели и он автоматически попадет во все ее реляции. Пример:

Представьте, что у нас есть 2 простых модели: User и Comment. Пользователь может писать комментарии, где каждый комментарий
должен быть закреплен за пользователем.

Модель пользователя
```php
<?php
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;

class User implements ModelInterface {
    
    /** @var Id */
    private $id;
    
    /** @var string */
    private $name;
    
    public function __construct(string $name) 
    {
        //Обратите внимание, мы создаем новый, пустой Id. Конкретное значение ему мы присвоим позже
        $this->id = new Id();
        $this->name = $name;
    }
    
    /**
    * Реализацию данного метода требует ModelInterface
    * @return Id
    */
    public  function getId(): Id
    {
        return $this->id;
    }   
    
    public  function getName(): string
    {
        return $this->name;
    }    
    
    /**
    * Реализацию данного метода требует ModelInterface
    * @return string
    */
    public static function getModelName() : string
    {
        return 'user';
    }    
}
```

Модель комментария
```php
<?php
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;

class Comment implements ModelInterface {
    
    /** @var Id */
    private $id;
    
    /** @var Id */
    private $userId;
    
    /** @var string */
    private $text;
    
    public function __construct(User $user, string $text) 
    {
        $this->id = new Id();
        $this->userId = $user->getId();
        $this->text = $text;
    }
    
    /**
    * Реализацию данного метода требует ModelInterface
    * @return Id
    */
    public  function getId(): Id
    {
        return $this->id;
    }
    
    public  function getUserId(): Id
    {
        return $this->userId;
    }
    
    public  function getText(): string
    {
        return $this->text;
    }
    
    /**
    * Реализацию данного метода требует ModelInterface
    * @return string
    */
    public static function getModelName() : string
    {
        return 'comment';
    }    
}
```

Теперь давайте посмотрим как будет работать связывание пользователя и комментария:
```php
<?php
$user = new User('Timur');
$comment = new Comment($user, 'Hello world!');
```
За счет того, что объекты в php передаются по ссылке, мы можем проставить для нашего пользователя какой-то постоянный Id,
который сразу попадет и в комментарий
```php
<?php
// Проставляем Id = 10
$user->getId()->setPermanentId(10);

echo $user->getId()->toString(); //Выведет '10'
echo $comment->getUserId()->toString(); //Также выведет '10'
```

Осталось только решить вопрос с тем, кто и как занимается простановкой Id. Об этом далее.

## IdGenerator
В DjinORM простановка перманентных Id осуществляется отдельным компонентом, который должен реализовывать интерфейс
[\DjinORM\Djin\Id\IdGeneratorInterface](src/Id/IdGeneratorInterface.php), который в свою очередь проставляет Id 
непосредственно перед записью модели в БД.

_Здесь стоит сделать отступление для тех, кто работал только с MySQL и привык к тому, что база сама проставляет Id. В 
случае с DjinORM такой подход невозможен, но это ничуть не плохо. Вместо этого вы можете использовать абсолютно любой 
счетчик, например Redis, sequences в PostgreSQL, строку UUID или даже использовать что-то вроде_ `SELECT MAX(id) FROM table`
_ для каждой генерации (хотя так делать не надо, потому что это приведет к блокировкам)_

В комплекте с ORM сразу идет 3 готовых Id-генератора (но вам ничего не мешает сделать свой)

- [\DjinORM\Djin\Id\UuidGenerator](src/Id/UuidGenerator.php) - не требует сторонних решений для генерации Id, возвращает 
случайную 128-битную строку вида `550e8400-e29b-41d4-a716-446655440000`. Используйте с осторожностью, т.к. использование
строки в качестве Id сильно "раздувает" размер БД
- [\DjinORM\Djin\Id\RedisIdGenerator](src/Id/RedisIdGenerator.php) - мы используем именно его. Он требует наличия ext-redis,
а также механизма, который в случае утраты данных в redis восстановит актуальное состояние счетчика используя что-то вроде 
`SELECT MAX(id) FROM table`
- [\DjinORM\Djin\Id\MemoryIdGenerator](src/Id/MemoryIdGenerator.php) - удобен для тестов. Последовательно генерирует числа 
начиная с заданного, хранит состояние в памяти, в переменной
- [\DjinORM\Djin\Id\IdGeneratorInterface](src/Id/IdGeneratorInterface.php) - сам интерфейс генератора. Имеет лишь один метод
`IdGeneratorInterface::getNextId(ModelInterface $model)` который на вход молучает модель, а на выходе отдает скальрное 
представление Id не проставляя при этом Id для самой модели. Благодаря таком подходу вы можете генерировать Id в зависимости
от класса и состояния модели


## Репозитории
В репозиториях лежит вся "грязь", ответственная за то, как находить, извлекать и сохранять модели из БД. Каждый репозиторий
должен реализовывать интерфейс [\DjinORM\Djin\Repository\RepositoryInterface](src/Repository/RepositoryInterface.php). 
Именно репозиторий имеет методы поиска, сохранения и удаления моделей. Именно он знает с какой именно БД вы работаете, и 
именно он знает как преобразовать вашу модель в массив данных для сохранения в БД и как обратно превратить этот массив в
вашу модель. Именно репозиторий отвечает за простановку перманентного Id, поэтому IdGenerator должен передаваться ему
через конструктор в качестве зависимости.

Подразумевается, что для этих целей используется [рефлексия](https://www.php.net/manual/ru/intro.reflection.php), при помощи
которой вы можете обращаться к приватным свойствам класса, получать и изменять их значения, создавать объекты без вызова
их конструктора и т.д. Чтобы упростить работу с моделями через рефлексию в DjinORM существует специальный хелпер
[\DjinORM\Djin\Helpers\RepoHelper](src/Helpers/ReflectionHelper.php).
 
С его помощью мы легко можем превратить нашу модель Comment в обычный массив
```php
<?php
use \DjinORM\Djin\Helpers\ReflectionHelper;

$user = new User('Timur');
$comment = new Comment($user, 'Hello world!');

$data = [
    'id' => ReflectionHelper::getProperty($comment, 'id')->toString(),
    'userId' => ReflectionHelper::getProperty($comment, 'userId')->toString(),
    'text' => ReflectionHelper::getProperty($comment, 'text'),
];
```

И обратно превратить массив, извлеченный из базы в модель
```php
<?php
use \DjinORM\Djin\Helpers\ReflectionHelper;
use \DjinORM\Djin\Id\Id;

$data = [
    'id' => 1,
    'userID' => 10,
    'text' => 'Hello world!',
];

$comment = ReflectionHelper::newWithoutConstructor(Comment::class);
ReflectionHelper::setProperty($comment, 'id', new Id($data['id']));
ReflectionHelper::setProperty($comment, 'userId', new Id($data['userId']));
ReflectionHelper::setProperty($comment, 'text', $data['text']);
```

Подобный подход дает полный контроль над хранением данных: вы можете извлекать объекты любого уровня вложенности, как угодно
трансформировать данные для записи в БД и делать с ними что угодно. В ряде случаев такой подход вполне оправдан, однако он
требует большого количества кода, особенно когда вам надо трансформировать сложные объекты и проверять существование каких-либо
значений. Кроме того, часто бывает так, что ряд сложных объектов встречается как вложенный в разных моделях.

О том, как решается данная проблема можно ознакомиться в следующем разделе.

### Мапперы

Первым делом стоит рассмотреть мапперы. Представьте, что мы решили сделать возможность создавать комментарии из примера 
выше анонимным пользователям. Т.е. у комментария нет автора. В таком случае его поле userId может быть Id, а может null.
При таком подходе нам придется каждый раз проверять `userId` на `null` и в зависимости от этого создавать/извлекать значение
Id или так и оставлять `null`. А теперь представьте, что помимо комментария у нас появятся такие модели как Post, Role и
куча любых других, которые тоже работают с Id. Поэтому логично вынести куда-то логику преобразования Id туда-сюда. Именно
по этой причине и появились мапперы.

Маппер умеет превращать данные определенного типа в скалярное представление или массив, а также, обратно превращать 
скалярные данные в нужный нам тип, осуществляя внутри себя все преобразорвания и проверки. Каждый маппер должен
реализовывать интерфейс [\DjinORM\Djin\Mappers\MapperInterface](src/Mappers/MapperInterface.php) - 
посмотрите код этого интерфейса, и вам многое станет понятно

В Djin поставляется набор типовых мапперов, которые могут конвертировать большинство распространенных типов данных:
- [\DjinORM\Djin\Mappers\IdMapper](src/Mappers/IdMapper.php) - преобразует 'строку' c id
в объект Id и обратно
- [\DjinORM\Djin\Mappers\ArrayMapper](src/Mappers/ArrayMapper.php) - маппер массивов. Если в конструктор передан другой
маппер, например IdMapper, то каждый элемент массива должен быть объектом Id. Если ни один из мапперов не передан, то массив
используется "как есть"
- [\DjinORM\Djin\Mappers\BoolMapper](src/Mappers/BoolMapper.php) - маппер булевых значений
- [\DjinORM\Djin\Mappers\DateTimeMapper](src/Mappers/DateTimeMapper.php) - маппер значений DateTime (принимает в конструктор 
строку формата даты и времени)
- [\DjinORM\Djin\Mappers\DateTimeImmutableMapper](src/Mappers/DateTimeMapper.php) - маппер значений DateTimeImmutable 
(принимает в конструктор строку формата даты и времени)
- [\DjinORM\Djin\Mappers\DeepIdentityMapper](src/Mappers/DeepIdentityMapper.php) - маппер сложных составных
конструкций, который позволяет сохранять и извлекать из БД любые наборы значений. Фактически, это аналог serialize() за
тем лишь исключением, что вместо сохранения имени класса при настройке маппера вы задаете алиасы имен классов. Такой
подход позволяет вам безболезненно переименовывать классы и менять их пространства имен не боясь за то, что репозиторий
не сможет воссоздать модель или объект нужного класса потому, что имя класса изменилось 
- [\DjinORM\Djin\Mappers\FloatMapper](src/Mappers/FloatMapper.php) - маппер вещественных чисел
- [\DjinORM\Djin\Mappers\IntMapper](src/Mappers/IntMapper.php) - маппер целых чисел
- [\DjinORM\Djin\Mappers\NullOrMapper](src/Mappers/NullOrMapper.php) - маппер, которые позволяет использовать значение null
вместо какого-либо маппера 
- [\DjinORM\Djin\Mappers\ObjectMapper](src/Mappers/ObjectMapper.php) - составной маппер, в который можено
передать набор любых других мапперов с целью конвертирования сложных составных объектов
- [\DjinORM\Djin\Mappers\RelationMapper](src/Mappers/LinkMapper.php) - маппер для реляций (о них 
читайте ниже)
- [\DjinORM\Djin\Mappers\StringMapper](src/Mappers/StringMapper.php) - маппер строковых значений


С использованием мапперов наши репозитории теперь могут выглядеть примерно следующим образом

```php
<?php
use DjinORM\Djin\Mappers\ObjectMapper;
use DjinORM\Djin\Mappers\IdMapper;
use DjinORM\Djin\Mappers\StringMapper;

class UserRepo implements \DjinORM\Djin\Repository\Repository 
{
    
    const TABLE_NAME = 'users';
    
    private $mapper;
    
    public function __construct() 
    {
        $this->mapper = new ObjectMapper(User::class, [
            'id' => new IdMapper(),
            'name' => new StringMapper(),
        ]);
    }
    
    public function findById($id) : ?ModelInterface
    {
        $sql = "SELECT * FROM {$this::TABLE_NAME} WHERE id = {$id}"; //Это лишь пример. Всегда используйте биндинги!
        ...
        $data = some_function_that_fetch_data($sql);
        
        //Здесь простой массив из базы будет превращен в объект User
        $model = $this->mapper->deserialize($data);
        return $model;
    }
    
    ...
    
    public function insert(ModelInterface $model)
    {
        //Здесь модель будет превращена в обычный массив
        $data = $this->mapper->serialize($model); //
        some_function_make_insert_sql($data);
    }
    
    ...
    
}

class CommentRepo implements \DjinORM\Djin\Repository\Repository 
{
    
    const TABLE_NAME = 'comments';
    
    private $mapper;
    
    public function __construct() 
    {
        $this->mapper = new ObjectMapper(Comment::class, [
            'id' => new IdMapper(),
            'userId' => new IdMapper(),
            'text' => new StringMapper(),
        ]);
    }
    
    public function findById($id) : ?ModelInterface
    {
        $sql = "SELECT * FROM {$this::TABLE_NAME} WHERE id = {$id}"; //Это лишь пример. Всегда используйте биндинги!
        ...
        $data = some_function_that_fetch_data($sql);
        
        //Здесь простой массив из базы будет превращен в объект Comment
        $model = $this->mapper->deserialize($data);
        return $model;
    }
    
    ...
    
    public function insert(ModelInterface $model)
    {
        //Здесь модель будет превращена в обычный массив
        $data = $this->mapper->serialize($model); //
        some_function_make_insert_sql($data);
    }
    
    ...
    
}
```

Разумеется, в реальном проекте вы можете использовать не только SQL базы, но и любые другие. Вы можете вынести общую логику
в абстрактный репозиторий и всячески оптимизировать код. В качестве примера можете посмотреть на SQL репозиторий 
[djin-repo-sql](https://github.com/djinorm/djin-repo-sql)

В репозитории мы можем делать что угодно: например в случае использования реляционных баз мы можем сохранять вложенные объекты
в другие таблицы, преобразовывать их в json или раскладывать по отдельным полям в формате точечной нотации. Вы сами упарвляете
тем, как ваши данные будут храниться.

## ModelManager
Задача компонента [\DjinORM\Djin\Manager\ModelManager](src/Manager/ModelManager.php) - собрать все вместе. Именно он максимально 
упрощает реальную работу с моделями, связывая модели, репозитории, согласованное сохранение данных и т.д. 

В конструктор ModelManager можно передать 4 параметра:
- PSR-совместимый контейнер (обязательно), который может возвращать объекты репозиториев по имени класса репозитория. Например, 
[PHP-DI](http://php-di.org/)
- callable onBeforeCommit(ModelManager $manager, array $modelsToSave, array $modelsToDelete) в которой можно осуществлять 
начало транзакции вашей СУБД
- callable onAfterCommit(ModelManager $manager, array $modelsToSave, array $modelsToDelete) в которой можно осуществлять 
commit транзакции вашей СУБД
- callable onCommitException(ModelManager $manager, array $modelsToSave, array $modelsToDelete) в которой можно осуществлять 
откат транзакции вашей СУБД

Также, нам нужно настроить ModelManager так, чтобы он знал с какими репозиториями и моделями ему предстоит работать. Для 
этого мы передаем ему репозиторий и модель или массив моделей, с которыми репозиторий может работать. Это бывает полезно
когда один репозиторий может сохранять несколько разных моделей, наследников какой-то общей модели

```php
<?php
$manager = new \DjinORM\Djin\Manager\ModelManager($container);
$manager->setModelRepository(UserRepo::class, User::class);
$manager->setModelRepository(CommentRepo::class, [Comment::class]);
```

Либо указание классов моделей можно опустить, если наш репозиторий сохраняет только одну модель. Модель будет определена 
согласно интерфейсу `\DjinORM\Djin\Repository\RepositoryInterface::getModelClass`

```php
<?php
$manager = new \DjinORM\Djin\Manager\ModelManager($container);
$manager->setModelRepository(UserRepo::class);
$manager->setModelRepository(CommentRepo::class);
```

Теперь мы можем работать с моделями примерно следующим образом
```php
<?php

$manager = new \DjinORM\Djin\Manager\ModelManager($container);
...

$user = new User('Timur');
$comment = new Comment($user, 'Hello world');

//Подготовит модели для сохранения в БД, но не запишет их в БД
$manager->persists([$user, $comment]);

//Если вдруг мы передумали сохранять модель, то можно вызвать метод delete(),
//который отменит сохранение новой, только что созданной модели, либо удалит
//уже существующую в базе модель
$manager->delete($comment);

//Достанет подготовленные для сохранения модели, вызовет методы репозиториев,
//проставляющие перманентные Id, выполнит onBeforeCommit, запишет модели
//в БД, и вызовет onAfterCommit или onCommitException в зависимости от результата
$manager->commit();

//Если же мы хотим найти какую-то модель, то мы можем сначала достать ее репозиторий
// несколькими способами. Напримр так
$userRepo = $manager->getModelRepository(User::class);

//или так
$userRepo = $manager->getModelRepository($user);

//или так
$userRepo = $manager->getRepositoryByModelName(User::getModelName());

//Находим пользователя. Это будет объект User
$user = $userRepo->findById(1);

//Изменяем пользователя
$user->setName('Anonim');

//Сохраняем изменения
$manager->persists($user);
$manager->commit();

//Удаляем пользователя. Вызов delete(), как и persists() не осуществляет запись в БД.
//Для фактического удаления модели из БД нужно вызвать commit();
$manager->delete($user);

//Реально удаляем запись из БД
$manager->commit();
```

## Реляции
Связь с использованием объекта Id работает быстро и хорошо. Но объект Id передается по ссылке только в момент создания 
записей. Если в момент создания User::$id и Comment::$userId ссылаются на один и тот же объект Id, то в последующих сессиях
при извлечении их из БД они будут ссылаться на разные объекты Id с одним и тем же перманентным значением. Обычно, это не 
создает проблем, т.к. значение самого Id изменить нельзя. Но Id сам по себе никак не связан с моделью. Например, в ситуации,
когда автором комментария может быть не только User, но и Bot, то как нам определить кто именно является автором комментария?

Именно для решения подобных задач существует [\DjinORM\Djin\Model\Relation](src/Model/Link.php). Давайте перепишем наш 
Comment так, чтобы он в качестве автора мог принимать кого угодно

```php
<?php
use DjinORM\Djin\Id\Id;
use DjinORM\Djin\Model\ModelInterface;
use DjinORM\Djin\Model\Link;

class Comment implements ModelInterface {
    
    /** @var Id */
    private $id;
    
    /** @var Link */
    private $author;
    
    /** @var string */
    private $text;
    
    public function __construct(ModelInterface $author, string $text) 
    {
        $this->id = new Id();
        $this->author = Link::to($author);
        $this->text = $text;
    }
    
    /**
    * Реализацию данного метода требует ModelInterface
    * @return Id
    */
    public  function getId(): Id
    {
        return $this->id;
    }
    
    public  function getAuthor(): Link
    {
        return $this->author;
    }
    
    public  function getText(): string
    {
        return $this->text;
    }
    
    /**
    * Реализацию данного метода требует ModelInterface
    * @return string
    */
    public static function getModelName() : string
    {
        return 'comment';
    }    
}
```

Реляции хранят в себе значение Id модели `ModelInterface::getId()` и ее имени из `ModelInterface::getModelName()`. Для
реляций также есть специальный маппер.

Теперь благодаря реляциям мы можем легко находить автора комментария, кто бы им ни был (любая модель)
```php
<?php

$manager = new \DjinORM\Djin\Manager\ModelManager($container);

$commentRepo = $manager->getModelRepository(Comment::class);
$comment = $commentRepo->findById(10);

$author = $manager->findByLink($comment->getAuthor());
```