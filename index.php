<?php
abstract class Request
{
    abstract protected function handle();
    public function handleRequest()
    {
        $this->handle();
        echo "Заявка обработана по паттерну Фабрика\n";
    }
}

class RequestMail extends Request
{   
    protected function handle(){
        echo "Заявка отправлена на E-mail\n";
    }
}
class RequestDb extends Request
{
    protected function handle(){
        echo "Заявка сохранена в таблицу\n";
    }
}

$requestMail = new RequestMail();
$requestDb = new RequestDb();
$requestMail->handleRequest();
$requestDb->handleRequest();