<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Advanced Algorithms Final Project Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        h2 {
            color: #34495e;
            border-left: 4px solid #3498db;
            padding-left: 10px;
            margin-top: 25px;
        }

        h3 {
            color: #2c3e50;
        }

        .section {
            margin-bottom: 30px;
        }

        .members {
            text-align: center;
            margin: 20px 0;
        }

        .member {
            display: inline-block;
            margin: 0 15px;
        }

        .algorithm-code {
            background: #f8f9fa;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 15px 0;
            font-family: monospace;
            white-space: pre-wrap;
        }

        .complexity-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .complexity-table th,
        .complexity-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .complexity-table th {
            background-color: #f2f2f2;
        }

        .observation-list {
            list-style-type: none;
            padding-left: 0;
        }

        .observation-list li {
            margin: 8px 0;
            padding-left: 20px;
            position: relative;
        }

        .observation-list li:before {
            content: "✓";
            color: #27ae60;
            position: absolute;
            left: 0;
        }
    </style>
</head>

<body>
    <h1>{{ $projectInfo['title'] }}</h1>

    <div class="members">
        @foreach ($projectInfo['members'] as $member)
            <div class="member"><strong>{{ $member }}</strong></div>
        @endforeach
    </div>

    <div class="section">
        <p><strong>Section:</strong> {{ $projectInfo['section'] }}</p>
        <p><strong>Demo Date:</strong> {{ $projectInfo['demoDate'] }}</p>
    </div>

    <div class="section">
        <h2>1. Problem Statement</h2>
        <h3>Real-World Problem</h3>
        <p>{{ $problemStatement['realWorldProblem'] }}</p>

        <h3>Significance</h3>
        <p>{{ $problemStatement['significance'] }}</p>

        <h3>Need for Algorithmic Solution</h3>
        <p>{{ $problemStatement['needForAlgorithm'] }}</p>
    </div>

    <div class="section">
        <h2>2. Objectives of the Project</h2>
        <h3>Primary Objectives</h3>
        <ul>
            @foreach ($objectives['primary'] as $objective)
                <li>{{ $objective }}</li>
            @endforeach
        </ul>

        <h3>Secondary Objectives</h3>
        <ul>
            @foreach ($objectives['secondary'] as $objective)
                <li>{{ $objective }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>3. Literature Review / Existing Solutions</h2>
        <h3>Existing Approaches</h3>
        <ul>
            @foreach ($literatureReview['existingApproaches'] as $approach)
                <li>{{ $approach }}</li>
            @endforeach
        </ul>

        <h3>Identified Gaps</h3>
        <ul>
            @foreach ($literatureReview['gaps'] as $gap)
                <li>{{ $gap }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>4. Proposed Solution / Methodology</h2>
        <h3>Methodology</h3>
        <p>{{ $proposedSolution['methodology'] }}</p>

        <h3>Algorithms Used</h3>
        <ul>
            @foreach ($proposedSolution['algorithmsUsed'] as $algorithm)
                <li>{{ $algorithm }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>5. Algorithm Design</h2>
        <h3>Pseudocode</h3>

        <h4>Greedy Weekly Limit Algorithm</h4>
        <div class="algorithm-code">{{ $algorithmDesign['pseudocode']['greedyWeeklyLimit'] }}</div>

        <h4>Hungarian Assignment Algorithm</h4>
        <div class="algorithm-code">{{ $algorithmDesign['pseudocode']['hungarianAssignment'] }}</div>

        <h4>Merge Sort Algorithm</h4>
        <div class="algorithm-code">{{ $algorithmDesign['pseudocode']['mergeSort'] }}</div>

        <h3>Correctness Discussion</h3>
        <ul>
            @foreach ($algorithmDesign['correctness'] as $algorithm => $discussion)
                <li><strong>{{ ucfirst($algorithm) }}:</strong> {{ $discussion }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>6. Complexity Analysis</h2>
        <h3>Time Complexity</h3>
        <table class="complexity-table">
            <tr>
                <th>Algorithm</th>
                <th>Time Complexity</th>
                <th>Description</th>
            </tr>
            @foreach ($complexityAnalysis['timeComplexity'] as $algorithm => $complexity)
                <tr>
                    <td>{{ $algorithm }}</td>
                    <td>{{ $complexity }}</td>
                    <td>Efficient for practical problem sizes</td>
                </tr>
            @endforeach
        </table>

        <h3>Space Complexity</h3>
        <table class="complexity-table">
            <tr>
                <th>Algorithm</th>
                <th>Space Complexity</th>
                <th>Description</th>
            </tr>
            @foreach ($complexityAnalysis['spaceComplexity'] as $algorithm => $complexity)
                <tr>
                    <td>{{ $algorithm }}</td>
                    <td>{{ $complexity }}</td>
                    <td>Memory efficient implementation</td>
                </tr>
            @endforeach
        </table>
    </div>

    <div class="section">
        <h2>7. Implementation Details</h2>
        <h3>Tools & Technologies</h3>
        <p><strong>Framework:</strong> {{ implode(', ', $implementation['tools']) }}</p>
        <p><strong>Technologies:</strong> {{ implode(', ', $implementation['technologies']) }}</p>

        <h3>Key System Components</h3>
        <ul>
            @foreach ($implementation['keyComponents'] as $component)
                <li>{{ $component }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>8. Experimental Evaluation / Results</h2>
        <h3>Testing Methodology</h3>
        <ul>
            @foreach ($experimentalResults['testing'] as $test)
                <li>{{ $test }}</li>
            @endforeach
        </ul>

        <h3>Key Observations</h3>
        <ul class="observation-list">
            @foreach ($experimentalResults['observations'] as $observation)
                <li>{{ $observation }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>9. Conclusion</h2>
        <h3>Summary</h3>
        <p>{{ $conclusion['summary'] }}</p>

        <h3>Future Work</h3>
        <ul>
            @foreach ($conclusion['futureWork'] as $work)
                <li>{{ $work }}</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h2>10. References</h2>
        <ol>
            @foreach ($references as $reference)
                <li>{{ $reference }}</li>
            @endforeach
        </ol>
    </div>
</body>

</html>
