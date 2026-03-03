<?php
// api/get_route.php
require_once '../includes/config.php';
require_once '../includes/db.php';
header('Content-Type: application/json');

$startNode = $_GET['start'] ?? '';
$endNode = $_GET['end'] ?? '';

if (empty($startNode) || empty($endNode)) {
    echo json_encode(['error' => 'Start and end nodes are required']);
    exit;
}

if ($startNode === $endNode) {
    echo json_encode(['path' => [], 'distance' => 0, 'message' => 'You are already there.']);
    exit;
}

try {
    $pdo = getDBConnection();
    
    // Fetch all nodes coordinates
    $nodesData = $pdo->query("SELECT id, name, x_coord, y_coord FROM nodes")->fetchAll(PDO::FETCH_ASSOC);
    $coordsMap = [];
    foreach ($nodesData as $n) {
        $coordsMap[$n['id']] = ['x' => $n['x_coord'], 'y' => $n['y_coord'], 'name' => $n['name']];
    }
    
    // Fetch all edges to build graph
    // DB has directed edges basically? In schema we indicated bidirectional handling here.
    $edgesData = $pdo->query("SELECT source_node, target_node, distance FROM edges")->fetchAll(PDO::FETCH_ASSOC);
    
    $graph = [];
    foreach ($nodesData as $n) {
        $graph[$n['id']] = [];
    }
    
    foreach ($edgesData as $e) {
        $src = $e['source_node'];
        $tgt = $e['target_node'];
        $dist = (float)$e['distance'];
        
        // Add undirected paths
        if (isset($graph[$src])) $graph[$src][$tgt] = $dist;
        if (isset($graph[$tgt])) $graph[$tgt][$src] = $dist;
    }

    // Dijkstra's Algorithm
    $distances = [];
    $previous = [];
    $unvisited = [];
    
    foreach ($graph as $node_id => $edges) {
        $distances[$node_id] = INF;
        $previous[$node_id] = null;
        $unvisited[$node_id] = true;
    }
    
    $distances[$startNode] = 0;

    while (count($unvisited) > 0) {
        // Find node with minimum distance
        $minNode = null;
        foreach ($unvisited as $node_id => $_) {
            if ($minNode === null || $distances[$node_id] < $distances[$minNode]) {
                $minNode = $node_id;
            }
        }
        
        // If smallest distance is infinity, remaining nodes are unreachable
        if ($distances[$minNode] == INF) {
            break;
        }
        
        // If target reached, we can stop
        if ($minNode === $endNode) {
            break;
        }
        
        unset($unvisited[$minNode]);
        
        foreach ($graph[$minNode] as $neighbor => $weight) {
            $alt = $distances[$minNode] + $weight;
            if ($alt < $distances[$neighbor]) {
                $distances[$neighbor] = $alt;
                $previous[$neighbor] = $minNode;
            }
        }
    }
    
    // Reconstruct path
    $path = [];
    $curr = $endNode;
    while (isset($previous[$curr])) {
        array_unshift($path, $curr);
        $curr = $previous[$curr];
    }
    
    if (!empty($path) || $startNode === $endNode) {
        array_unshift($path, $startNode);
        
        // Generate SVG path coordinate string and calculate steps
        $svgPoints = [];
        $steps = [];
        foreach ($path as $p) {
            if (isset($coordsMap[$p])) {
                $svgPoints[] = $coordsMap[$p]['x'] . ',' . $coordsMap[$p]['y'];
                $steps[] = $coordsMap[$p]['name'];
            }
        }
        
        $svgPathData = "M " . implode(" L ", $svgPoints);
        
        echo json_encode([
            'success' => true,
            'distance' => round($distances[$endNode], 2),
            'path_ids' => $path,
            'steps' => $steps,
            'svg_path' => $svgPathData,
            'dest_x' => $coordsMap[$endNode]['x'],
            'dest_y' => $coordsMap[$endNode]['y']
        ]);
        
    } else {
        echo json_encode(['error' => 'No path found between these locations.']);
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
