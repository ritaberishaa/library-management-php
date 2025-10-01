<?php
namespace App\Core;
class Router {
  private array $routes = ['GET'=>[], 'POST'=>[]];
  public function get(string $p, string $a): void { $this->routes['GET'][$p] = $a; }
  public function post(string $p, string $a): void { $this->routes['POST'][$p] = $a; }
  public function dispatch(): void {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $action = $this->routes[$method][$uri] ?? null;
    if (!$action) { http_response_code(404); echo "404 Not Found"; return; }
    [$c, $m] = explode('@', $action);
    $fqcn = "App\\Controllers\\$c";
    $instance = new $fqcn();
    $instance->$m();
  }
}
