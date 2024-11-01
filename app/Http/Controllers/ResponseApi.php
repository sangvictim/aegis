<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ResponseApi extends JsonResponse
{
  protected string $message;
  protected string $title;
  protected mixed $originalData = [];

  function __construct()
  {
    $this->message = 'OK';
    $this->title = 'Response Title';

    parent::__construct();
  }

  public function message(string $message)
  {
    $this->message = $message;
    return $this->synchronizeData();
  }

  public function getMessage()
  {
    return $this->message;
  }

  public function title(string $title)
  {
    $this->title = $title;
    return $this->synchronizeData();
  }

  public function getTitle()
  {
    return $this->title;
  }

  public function data(mixed $data): static
  {
    $this->originalData = $data;
    return $this->synchronizeData();
  }

  public function getOriginData(): mixed
  {
    return $this->originalData;
  }

  public function error(string $error)
  {
    return parent::setData([
      'message' => $error,
      'title' => $this->getTitle(),
    ]);
  }

  public function formError(mixed $errors): static
  {
    return parent::setData([
      'message' => $this->getMessage(),
      'title' => $this->getTitle(),
      'errors' => $errors
    ]);
  }

  protected function synchronizeData(): static
  {
    return parent::setData([
      'message' => $this->getMessage(),
      'title' => $this->getTitle(),
      'data' => $this->getOriginData()
    ]);
  }

  public function statusCode(int $code): static
  {
    return $this->setStatusCode($code);
  }

  public function setHeader(array $headers): static
  {
    $header = array_merge(['Access-Control-Allow-Origin' => '*'], $headers);
    return $this->withHeaders($header);
  }
}
