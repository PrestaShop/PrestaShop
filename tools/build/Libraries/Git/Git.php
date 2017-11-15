<?php

class Git
{
    public static function checkoutFiles($folder, $files = [])
    {
        $files = (array)$files;

        foreach ($files as $file) {
            $command = 'cd '.$folder.' && git checkout -- '.$file;
            self::executeQuery($command);
        }

        return true;
    }

    public static function checkoutBranch($folder, $branch)
    {
        $command = 'cd '.$folder.' && git checkout '.$branch;
        return self::executeQuery($command);
    }

    public static function cloneRepo($source, $dest, $params = [])
    {
        $params = self::getParams($params);
        return self::executeQuery('git clone '.$params.' https://github.com/'.$source.' '.$dest);
    }

    public static function commit($folder, $message, $email, $author)
    {
        $command = 'cd '.$folder.' && git config user.name "'.$author.'" && git config user.email "'.$email.'"';
        self::executeQuery($command);

        try {
            $command = 'cd '.$folder.' && git commit -m \''.$message.'\' --author="'.$author.' <'.$email.'>"';

            return self::executeQuery($command);
        } catch (Exception $ex) {
            if ($ex->getCode() != 1) {
                throw new Exception($ex);
            }
        }

        return false;
    }

    public static function commitAll($folder, $message, $email, $author)
    {
        $command = 'cd '.$folder.' && git add --all';
        self::executeQuery($command);

        return self::commit($folder, $message, $email, $author);
    }

    public static function commitFiles($folder, $files, $message)
    {
        if (count($files) == 0) {
            Throw new Exception('Files list cannot be empty');
        }

        $user = auth()->user();
        $files = implode(' ', $files);
        self::executeQuery('cd '.$folder.' && git add '.$files);

        return self::commit($folder, $message, $user->github_email, $user->github_name);
    }

    public static function executeQuery($command, &$output = [])
    {
        exec($command, $output, $return);

        if ($return != 0) {
            throw new Exception(implode('<br/>', $output) , $return);
        }

        return true;
    }

    public static function getChanges($folder)
    {
        $changes = [];
        Git::executeQuery('cd '.$folder.' && git diff --name-only', $files);

        foreach ($files as $file) {
            Git::executeQuery('cd '.$folder.' && git diff --no-color '.$file, $changes[$file]);
        }

        return $changes;
    }

    public static function getParams($params)
    {
        $params_str = '';

        foreach ($params as $key => $value) {
            if (strpos($value, '--') === 0) {
                $params_str .= ' '.$value;
            } elseif (strpos($key, '-') === 0) {
                $params_str .= ' '.$key.' '.$value;
            } else {
                $params_str .= ' '.$value;
            }
        }

        return $params_str;
    }

    public static function merge($folder, $repository, $from, $to)
    {
        if (Request::has('github_password') == false) {
            return false;
        }

        $command = 'cd '.$folder.' && git checkout '.$to.' && git merge --ff '.$from;
        self::executeQuery($command);
        return self::push($folder, $repository);
    }

    public static function pull($folder, $params = [])
    {
        $params = self::getParams($params);
        $command = 'cd '.$folder.' && git pull'.$params;

        return self::executeQuery($command);
    }

    public static function push($folder, $repository, $params = [])
    {
        if (Request::has('github_password') == false) {
            return false;
        }

        $user = auth()->user();
        try {
            $command = 'cd '.$folder.' && git config user.name "'.$user->github_name.'" && git config user.email "'.$user->github_email.'"';
            self::executeQuery($command);
        } catch (Exception $ex) {
            throw new Exception('Cannot update git config', $ex->getCode(), $ex);
        }

        try {
            $params = self::getParams($params);
            $command = 'cd '.$folder.' && git push https://'.$user->github_login.':'.e(Input::get('github_password')).'@github.com/'.$repository.$params;
            return self::executeQuery($command, $output);
        } catch (Exception $ex) {
            throw new Exception('Cannot push to the remote ('. $ex->getMessage() .')', $ex->getCode(), $ex);
        }
    }

}
