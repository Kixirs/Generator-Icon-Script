# Generator Icon Script (GIS)

**Generator Icon Script** — це веб-платформа для пошуку та використання готових векторних іконок для проєктів, а також AI-інструмент для генерації індивідуальних іконок за текстовим описом (промптом).

**Основні можливості:**
- 🎨 Каталог готових векторних іконок з можливістю фільтрації та пошуку
- 🤖 AI-генерація унікальних іконок за описом користувача
-  Система обраних іконок (Favorites)
- 📊 Система рейтингів та оцінок іконок
- 👥 Авторизація та ролі користувачів (User, Admin)
- 📥 Завантаження іконок у форматі SVG

---

## Структура проєкту
Generator-Icon-Script/ 
│ 
├── 📁 css/ # Стилі для веб-інтерфейсу 
│ └── *.css # CSS файли 
│ 
├── 📁 database/ # Робота з базою даних 
│ ── db.php # Підключення до MySQL (GIScript) 
│ 
├── logi/ # Логи системи 
│ └── delete_log.txt # Лог видалень 
│ 
├── 📁 php/ # PHP-логіка (існуючий бекенд) 
│ ├── Index.php # Головна сторінка 
│ ├── Login.php # Авторизація 
│ ├── Reg.php # Реєстрація 
│ ├── admin.php # Панель адміністратора 
│ ├── icon.php # Робота з іконками 
│ ├── favorite.php # Обране 
│ ├── rate.php # Рейтинги 
│ └── ... 
│ 
├── 📁 GIS.Api/ # ASP.NET Core Web API (новий бекенд) 
│ ├── 📁 GIS.Api.Web/ # Web-шар (Controllers, Program.cs) 
│ │ ├── Controllers/ 
│ │ │ ├── IconsController.cs 
│ │ │ ├── UsersController.cs 
│ │ │ └── AdminController.cs 
│ │ ├── Program.cs 
│ │ ├── appsettings.json 
│ │ └── GIS.Api.Web.csproj 
│ │ 
│ ├── 📁 GIS.Api.Core/ # Бізнес-логіка 
│ │ ├── Entities/ # Entity-моделі 
│ │ ├── DTOs/ # Data Transfer Objects 
│ │ ├── Interfaces/ # Інтерфейси сервісів 
│ │ ├── Services/ # Реалізація сервісів 
│ │ └── Exceptions/ # Кастомні винятки 
│ │ 
│ ├── GIS.Api.Infrastructure/ # Робота з даними 
│ │ ├── Data/ 
│ │ │ └── AppDbContext.cs 
│ │ ├── Migrations/ # EF Core міграції 
│ │ └── Repositories/ 
│ │ 
│ └── 📁 GIS.Api.Tests/ # Тести 
│ ├── UnitTests/ 
│ ── IntegrationTests/ 
│ 
├── 📁 web-design/ # Дизайн веб-інтерфейсу 
├── 📁 javaScript/ # Frontend-логіка 
└── README.md


## Технології
### Backend (існуючий)
- **PHP** — серверна мова програмування
- **MySQL** — реляційна база даних
- **PDO** — інтерфейс для роботи з БД

### Backend (новий — API)
- **ASP.NET Core 8 Web API** — фреймворк для створення REST API
- **C#** — мова програмування
- **Entity Framework Core** — ORM для роботи з базою даних
- **AutoMapper** — мапінг між Entity та DTO
- **Swagger/OpenAPI** — документація API
- **xUnit** — фреймворк для тестування
- **Moq** — бібліотека для мокування в тестах

### Frontend
- **HTML5/CSS3** — розмітка та стилі
- **JavaScript** — інтерактивність
- **SVG** — формат векторних іконок

### Інструменти
- **Git/GitHub** — система контролю версій
- **GitHub Projects** — Kanban-дошка для управління завданнями
- **Visual Studio / VS Code** — IDE для розробки

---

## Залежності

### Для ASP.NET Core API проєкту

**Основні NuGet-пакети:**



## Запуск проєкту
Інструкція зі встановлення залежностей та запуску.

## Команда
Перелік учасників команди та їхні ролі.

```xml
<!-- GIS.Api.Web -->
<PackageReference Include="Microsoft.EntityFrameworkCore.Design" Version="8.0.0" />
<PackageReference Include="Swashbuckle.AspNetCore" Version="6.5.0" />

<!-- GIS.Api.Core -->
<PackageReference Include="AutoMapper.Extensions.Microsoft.DependencyInjection" Version="12.0.1" />
<PackageReference Include="FluentValidation" Version="11.9.0" />

<!-- GIS.Api.Infrastructure -->
<PackageReference Include="Microsoft.EntityFrameworkCore" Version="8.0.0" />
<PackageReference Include="Pomelo.EntityFrameworkCore.MySql" Version="8.0.0" />

<!-- GIS.Api.Tests -->
<PackageReference Include="xunit" Version="2.6.6" />
<PackageReference Include="xunit.runner.visualstudio" Version="2.5.6" />
<PackageReference Include="Moq" Version="4.20.70" />
<PackageReference Include="Microsoft.AspNetCore.Mvc.Testing" Version="8.0.0" />
<PackageReference Include="Microsoft.EntityFrameworkCore.InMemory" Version="8.0.0" />
```


GIS - платформа де можна знайти різні векторні іконки для своїх проєктів. Також для індивідуальних іконок встроєний AI який по опису згенерує вам іконку по вашому промту

##Для PHP-частини
PHP 7.4+
MySQL 5.7+
PDO MySQL драйвер

#Запуск проєкту
1. Клонувати репозиторій
   
         ```git clone https://github.com/Kixirs/Generator-Icon-Script.git
            cd Generator-Icon-Script```

2. Налаштування бази даних
  1. Створіть базу даних GIScript у MySQL:
     
```CREATE DATABASE GIScript CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;```

  2. Оновіть підключення у файлі database/db.php:

    ```$host = 'localhost';
       $db = 'GIScript';
       $user = 'root';
       $pass = 'ваш_пароль';```

3. Запуск ASP.NET Core API
   
# Перейти до API проєкту
cd GIS.Api

# Відновити залежності
dotnet restore

# Застосувати міграції бази даних
dotnet ef database update --project GIS.Api.Infrastructure --startup-project GIS.Api.Web

# Запустити API
dotnet run --project GIS.Api.Web

PI буде доступний за адресою: https://localhost:5001 або http://localhost:5000

Swagger UI: https://localhost:5001/swagger

#Документація API
Після запуску ASP.NET Core API, Swagger UI доступний за адресою:

```https://localhost:5001/swagger```


##Структура                                 

——————————————————                                                   

Структура поділена на декілька гілок:
  Main - головна гілка , в неї відбувається pull requests тільки після перевірки на помилки, усунення помилок, тестування. І тільки тоді, коли не виникає ніяких помилок буде проводитися pull requests. 

  GIS/api - ця гілка потрібна для створення тільки API і нічого іншого. Також можна робити коміти тільки в свою гілку. pull requests також робиться тільки після тестування виправлення всіх помиилок та повнорного тестування. 

  GIS/database - гілка яка організована тільки для бази даних. Наразі база даних заповнена але в майбутньому дані будуть змінюватись , тому потрібно буде оновлювати актуальні данні. (Нижче буде приклад команд для роботи)

  GIS/web-desing - все просто, структура css.

  GIS/php - гілка для роботи з безпосередньо файлами php.

  GIS/javaScript - гілка для компонентів що можна реалізувати через javaScript.

——————————————————                 

Приклади коду для роботи з Git Bash

——————————————————               
     
  Спершу для роботи з гітом потрібно завантажити абсолютно всі зміни у файли. Це можна зробити за допомогою наступної команди:

          git pull

Якщо ж потрібно отримати данні з якоїсь одної гілки то просто потрібно написати цейже самий код і додати ще назву гілки. Ось приклад

          git pull origin GIS/web-design

Для завантаження файлів на гід після роботи потрібно перевірити на якій гілкі ви знаходитесь. Якщо не на тій гілці о потрібно , то використайте наступну команду.

          git checkout (та назва гілки , наприклад GIS/database)
          
Це потрібно для того щоб переміститись на потрібну вам гілку. 

І ось тепер можна дійти до зберігання файлів на Github. ДПісля того як ви створили/оновили файл потрібно написати 

          git add .
          
Після цього потрібно написати команду що відповідає за коміт. А точніше 

          git commit -m "Назва коміту"

В назві коміту бажано писати коротко що було зроблено. Доприкладу: створено табличку users, або додано новий функціонал до панелі адміністратора.

І тепер щоб цей коміт був актуальним його потрібно відравити на веб-сайт гіту. Це можна зробити за допомогою наступної команди:

          git push

Якщо потрібно запушити в конкретну гілку то там вже використовується команда 

          git push -u origin (і назва гілки)

Наступна команда відповідає за перегляд гілки на якій ви знаходитесь , та показує нові файли

          git status

Далі в нас йде команда для перегляду всіх гілок. 

          git branch

Вона покаже всі гілки та підсвітить гілку на якій ви зараз знаходитесь.



          


