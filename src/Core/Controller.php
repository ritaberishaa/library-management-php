<?php
namespace App\Core;

class Controller {
  protected function view(string $tpl, array $data = []): void {
    extract($data);
    $viewPath = __DIR__ . '/../Views/' . $tpl . '.php';
    $layout = __DIR__ . '/../Views/layout.php';
    ob_start(); require $viewPath; $content = ob_get_clean();
    require $layout;
  }

  protected function redirect(string $url): void {
    header("Location: $url");
    exit;
  }

  protected function setFlash(string $type, string $message): void {
    $_SESSION["flash_{$type}"] = $message;
  }
}
