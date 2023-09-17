    <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class CreatePostsTable extends Migration
{
    protected $tableName;
    
    public function __construct()
    {
        $this->tableName = config('postify.table', 'posts');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->defaultTableSchema();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropDefaultTable();
    }

    public function createTableSchema($tableName)
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('content');
            $table->string('description');
            $table->string('excerpt')->nullable();
            $table->boolean('published')->default(false);
            $table->dateTime('published_at')->default(now());
            $table->boolean('comments_status')->default(false);
            $table->foreignId('media_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });
    }

    public function defaultTableSchema()
    {
        $this->createTableSchema($this->tableName);
    }

    public function dropDefaultTable()
    {
        Schema::dropIfExists($this->tableName);
    }
}
