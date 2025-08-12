<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Facades\Log;
use Exception;

class SystemController extends Controller
{
    public function checkSistemProcesses(Request $request)
    {
        $remoteServer = '202.51.198.43';
        $username = $request->input('username');
        $password = $request->input('password');
        $port = 22;

        try {
            //Log::info("Attempting SSH connection to {$remoteServer}:{$port} with username {$username}");

            $ssh = new SSH2($remoteServer, $port);
            if (!$ssh->login($username, $password)) {
                Log::error("SSH login failed for {$username}@{$remoteServer}:{$port}");
                return response()->json([
                    'message' => 'Failed to execute command on remote server',
                    'error' => 'Login failed'
                ], 500);
            }

            //Log::info("SSH login successful for {$username}@{$remoteServer}:{$port}");
            $output = $ssh->exec('ps aux | grep js');

            return response()->json(['output' => explode("\n", trim($output))], 200);
        } catch (Exception $e) {
            //Log::error('SSH Connection Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to execute command on remote server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function runRemoteSistem(Request $request)
    {
        $remoteServer = '202.51.198.43';
        $username = $request->input('username');
        $password = $request->input('password');
        $port = 22;
        $filePath = '/var/www/modem/index.js'; 

        try {
            //Log::info("Attempting SSH connection to {$remoteServer}:{$port} with username {$username}");

            $ssh = new SSH2($remoteServer, $port);
            if (!$ssh->login($username, $password)) {
                Log::error("SSH login failed for {$username}@{$remoteServer}:{$port}");
                return response()->json([
                    'message' => 'Failed to execute command on remote server',
                    'error' => 'Login failed'
                ], 500);
            }

            //Log::info("SSH login successful for {$username}@{$remoteServer}:{$port}");
            $output = $ssh->exec("node $filePath");

            return response()->json(['message' => 'JS file executed successfully', 'output' => $output], 200);
        } catch (Exception $e) {
            //Log::error('SSH Connection Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to execute command on remote server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showSistemProcesses()
    {
        return view('pages.sistem.index');
    }
}