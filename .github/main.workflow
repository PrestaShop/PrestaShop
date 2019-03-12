workflow "Code Quality" {
  on = "push"
  resolves = [
    "PHP-CS-Fixer",
  ]
}

action "PHP-CS-Fixer" {
  uses = "docker://oskarstark/php-cs-fixer-ga"
  args = "--config=.php_cs.dist --diff --diff-format=udiff --dry-run"
}
