# adev\lrc
A Laravel package to generate column constants for your models based on their table schema. This helps improve consistency and readability in your application, especially when referring to table columns in your code.

## Features
Automatically generates constants for each column in your model.
Generates a TABLE_NAME constant for each model.
Easy to use in migrations, controllers, and other parts of your application.

## Installation
You can install the package via Composer. Run the following command in your Laravel project:

```bash
composer require lrc/laravel-column-constants
```

Once installed, the package will automatically register the Artisan command.

## Usage

After the package is installed, you can use the make:constants Artisan command to generate column constants for a given model.

Generate Constants for a Single Model
To generate constants for a specific model, run:

```bash
php artisan make:constants User
```

This will generate the following constants in your User model (assuming your model is located in app/Models/User.php):

```bash

public const TABLE_NAME = 'users';
public const COL_ID = 'id';
public const COL_NAME = 'name';
public const COL_EMAIL = 'email';
// and so on for each column in the 'users' table

```
## Generate Constants for All Models
To generate constants for all models in your app/Models directory, run:

```bash
php artisan make:constants all
```
This will loop through all models and generate column constants for each one.

## How to Use the Constants
Once the constants are generated in your models, you can easily use them throughout your application, for example in migrations, controllers, or queries. Here's how you can utilize these constants:

## 1. In Migrations
When creating or modifying database tables in migrations, you can reference the constants instead of hardcoding column names.

 ```bash
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create(User::TABLE_NAME, function (Blueprint $table) {
            $table->id(User::COL_ID);
            $table->string(User::COL_NAME);
            $table->string(User::COL_EMAIL)->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(User::TABLE_NAME);
    }
}
``` 

## 2. In Controllers
Using constants makes it easier to refer to column names in your queries or when manipulating data in controllers.

```bash
use App\Models\User;

class UserController extends Controller
{
    public function show($id)
    {
        $user = User::where(User::COL_ID, $id)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::where(User::COL_ID, $id)->first();

        if ($user) {
            $user->update([
                User::COL_NAME => $request->name,
                User::COL_EMAIL => $request->email,
            ]);
        }

        return response()->json($user);
    }
}
```

## 3. In Seeders
You can also use the constants in seeders to populate the database.

```bash
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            User::COL_NAME => 'John Doe',
            User::COL_EMAIL => 'john.doe@example.com',
        ]);
    }
}
```
## 4. In Queries
You can use the constants when building queries, which improves readability and reduces the chance of errors due to typos.

```bash
use App\Models\User;

// Using constants for column names
$users = User::where(User::COL_NAME, 'like', '%John%')
             ->orderBy(User::COL_EMAIL)
             ->get();
```

## Benefits of Using Constants
Prevents Hardcoding: Instead of hardcoding column names as strings, you can use the constants, which improves maintainability and reduces the chances of errors caused by typos.
Improves Readability: Using constants like User::COL_NAME makes it clear that you're referring to a database column, not just any string.
Easy Refactoring: If you ever change the column name in the database or the model, you only need to update it in the constants, rather than in every place where the column is referenced in the code.
Configuration (Optional)
If you'd like to customize the behavior of the package (such as the location of your models or other settings), you can publish the configuration file.

```bash
php artisan vendor:publish --provider="LRC\Providers\CommandConstantsServiceProvider" --tag="config"
```

This will publish the command_constants.php file to your config/ directory, where you can adjust the settings.

## License
This package is open-source and available under the MIT License.

# Example: Complete Workflow
Let's walk through a complete example where you create a User model, generate column constants, and use them in a migration, controller, and seeder.

## 1 Create the Model:

If you haven't created the model yet, run:

```bash
php artisan make:model User
```
This will generate a model file at app/Models/User.php.

## 2 Run the Command to Generate Constants:

After the User model is generated, run the command to generate constants:

```bash
php artisan make:constants User
```
This will add constants like COL_NAME, COL_EMAIL, and TABLE_NAME in the User model.

## 3 Use Constants in the Migration:

Create a migration to create the users table (if not already done):

```bash
php artisan make:migration create_users_table
```
In the generated migration, use the constants as shown earlier.

## 4 Use Constants in the Controller:

Now, in the UserController, use these constants to refer to the columns.

## 5 Use Constants in the Seeder:

You can populate the users table in your UserSeeder using the constants.

# Conclusion
By using this package, you can improve the maintainability and readability of your Laravel project by generating and using constants for column names in your models. It ensures that column names are consistent across your application, making it easier to manage as your project grows.

 

# Contact, Support, or Donations
If you would like to contact me for any reason, including support, questions, or contributions to this project, feel free to reach out via:

Email: [adilelkhalloufi@gmail.com]
GitHub: https://github.com/adilelkhalloufi
 
# Donations
If you want to support the development of this project, you can make a donation via the following options:
 
[PayPal](https://www.paypal.com/donate/?hosted_button_id=2MDDDBX75Z3UQ)
Any donation is appreciated and will go toward improving this project.

Contributing
I welcome contributions! If you'd like to contribute to the project, please follow the instructions in the Contributing Guidelines or simply open an issue or pull request.