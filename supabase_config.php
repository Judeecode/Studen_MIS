<?php
// supabase_config.php
session_start();

// Supabase Configuration
define('SUPABASE_URL', 'https://qgsvyfavosfywgdkdmkd.supabase.co');
define('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InFnc3Z5ZmF2b3NmeXdnZGtkbWtkIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTkxMTE1NTEsImV4cCI6MjA3NDY4NzU1MX0.BcVL-IoNLmegX7w293XKI8h1auzcGL6INlzTZdeaoig');

// Supabase Helper Class
class SupabaseClient {
    private $url;
    private $key;
    
    public function __construct() {
        $this->url = SUPABASE_URL;
        $this->key = SUPABASE_ANON_KEY;
    }
    
    // Execute a query using Supabase REST API
    public function query($table, $method = 'GET', $data = null, $filters = []) {
        $endpoint = $this->url . '/rest/v1/' . $table;
        
        // Add filters to URL
        if (!empty($filters)) {
            $queryString = http_build_query($filters);
            $endpoint .= '?' . $queryString;
        }
        
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
        
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        }
        
        return false;
    }
    
    // Select records
    public function select($table, $columns = '*', $filters = []) {
        $endpoint = $this->url . '/rest/v1/' . $table . '?select=' . $columns;
        
        // Add filters
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                // Handle operators like eq, gt, lt, etc.
                $operator = $value['operator'] ?? 'eq';
                $filterValue = $value['value'];
                $endpoint .= '&' . $key . '=' . $operator . '.' . urlencode($filterValue);
            } else {
                $endpoint .= '&' . $key . '=eq.' . urlencode($value);
            }
        }
        
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
    
    // Insert record
    public function insert($table, $data) {
        return $this->query($table, 'POST', $data);
    }
    
    // Update record
    public function update($table, $data, $filters) {
        $endpoint = $this->url . '/rest/v1/' . $table;
        
        // Add filters
        $first = true;
        foreach ($filters as $key => $value) {
            $endpoint .= ($first ? '?' : '&') . $key . '=eq.' . urlencode($value);
            $first = false;
        }
        
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ];
        
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return ($httpCode >= 200 && $httpCode < 300);
    }
    
    // Delete record
    public function delete($table, $filters) {
        $endpoint = $this->url . '/rest/v1/' . $table;
        
        // Add filters
        $first = true;
        foreach ($filters as $key => $value) {
            $endpoint .= ($first ? '?' : '&') . $key . '=eq.' . urlencode($value);
            $first = false;
        }
        
        $headers = [
            'apikey: ' . $this->key,
            'Authorization: Bearer ' . $this->key,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return ($httpCode >= 200 && $httpCode < 300);
    }
}

// Initialize Supabase client
$supabase = new SupabaseClient();

// Helper functions for authentication
function is_logged_admin() { 
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'); 
}

function is_logged_teacher() { 
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'teacher'); 
}

function is_logged_student() { 
    return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student'); 
}
?>
