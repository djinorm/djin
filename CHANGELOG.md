# Changelog

All notable changes to `DjinORM\Djin` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 3.0.0 - 2017-12-12

### Changed
- Присвоением ID для моделей теперь занимается репозиторий
- ModelManager требует DI-контейнер в конструктор, и создает классы репозиториев только по мере необходимости

### Removed
- Удален класс ModelConfig
- Удален метод RepositoryInterface::isTransactional()

## 2.0.0 - 2017-10-24

### Added
- Метод [RepositoryInterface::freeUpMemory](src/Repository/RepositoryInterface.php), который освобождает из памяти 
загруженные модели и его реализация в [Repository](src/Repository/Repository.php)
- Метод [ModelManager::freeUpMemory](src/Manager/ModelManager.php), который освобождает из памяти все модели во всех 
репозиториях

### Changed
- Метод [RepositoryInterface::getModelClass](src/Repository/RepositoryInterface.php) метод теперь должен быть публичным
- [RepoHelper.php](src/Helpers/RepoHelper.php) в большинстве методов теперь может принимать третьим параметром не только 
массив, но и скалярные значения
- Изменен порядок и тип передаваемых аргументов в  [ModelManager::setModelConfig](src/Manager/ModelManager.php). Теперь
в качестве первого аргумента идет репозиторий, а вторым аргументом идет модель. Если не указывать модель, то ее класс будет 
взят из репозитория. Также, вместо одного класса модели тепреь можно указывать их массив 

### Removed
- Метод [\DjinORM\Djin\Id\Id::getId](src/Id/Id.php)
