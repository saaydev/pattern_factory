<?php
interface InterfaceWorkout
{
    public function getPoints(): int;
}
interface InterfaceTask
{
    public function getPoints(): int;
}
interface InterfacePost
{
    public function getLength(): int;
}

abstract class Handle
{
    protected string $host = "localhost";
    protected string $username = "root";
    protected string $userpass = "";
    protected string $dbname = "test_factory";
    protected string $table = "workout";
    abstract function factoryMethod(): InterfaceWorkout;
    public function applyPoints()
    {
        $workout = $this->factoryMethod();
        $value = $workout->getPoints();
        try 
        {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $mysqli = new mysqli($this->host, $this->username, $this->userpass);
            if(!$mysqli->connect_error){
                $mysqli->query("CREATE DATABASE IF NOT EXISTS {$this->dbname}");
                $mysqli->select_db($this->dbname);
                $mysqli->query("CREATE TABLE IF NOT EXISTS workouts (id INTEGER AUTO_INCREMENT PRIMARY KEY, value VARCHAR(50))");
                $mysqli->query("INSERT INTO workouts (value) VALUES ('$value')");
            }
        } 
        catch (\Throwable $th) 
        {
            printf("Error mysqli: %s\n", $th->getMessage());
        }
    }
}

class Task implements InterfaceTask
{
    protected int $points;
    public function __construct($points)
    {
        $this->points = $points;
    }
    public function getPoints(): int
    {
        return $this->points;
    }   
}
class Post implements InterfacePost
{
    protected string $message;
    public function __construct(string $message)
    {
        $this->message = $message;
    }
    public function getLength(): int
    {
        return strlen($this->message);
    }
}

class WorkoutStrava extends Handle implements InterfaceWorkout
{
    protected InterfaceTask $task;
    public function __construct(InterfaceTask $task, int $time = 0, int $distance = 0)
    {
        $this->task = $task;
        $this->time = $time;
        $this->distance = $distance;
    }
    public function factoryMethod(): InterfaceWorkout
    {
        return $this;
    }
    public function getPoints(): int
    {
        return $this->task->getPoints() * ($this->distance / $this->time);
    }
}

class WorkoutPostVk extends Handle implements InterfaceWorkout
{
    protected InterfaceTask $task;
    protected InterfacePost $post;
    public function __construct(InterfaceTask $task, InterfacePost $post)
    {
        $this->task = $task;
        $this->post = $post;
    }
    public function factoryMethod(): InterfaceWorkout
    {
        return $this;
    }
    public function getPoints(): int
    {
        return $this->task->getPoints() * $this->post->getLength();
    }
}
$taskStrava = new Task(10);
$taskPostVk = new Task(1);
$post       = new Post("This message of post vk");

$workoutStrava = new WorkoutStrava($taskStrava, 1000, 10000);
$workoutPostVk = new WorkoutPostVk($taskPostVk, $post);

$workoutStrava->applyPoints();
$workoutPostVk->applyPoints();