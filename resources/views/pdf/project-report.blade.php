<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Report: {{ $project->name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', 'Roboto', sans-serif;
            line-height: 1.3;
            color: #2d3748;
            background: #ffffff;
        }

        .container {
            max-width: 210mm;
            margin: 0 auto;
            padding: 15px;
        }

        /* Header Styles */
        .report-header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px 0;
            color: #2d3748;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border-radius: 10px;
      
        }

        .report-header h1 {
            font-size: 22px;
            margin-bottom: 5px;
            color: #1e3a8a;
        }

        .report-header p {
            font-size: 12px;
            color: #2d3748;
        }

        /* Section Styling */
        .report-section {
            margin-bottom: 20px;
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.03);
        }

        .section-title {
            font-size: 18px;
            color: #1e3a8a;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        /* Metadata Grid */
        .metadata-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .metadata-card {
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            border-left: 4px solid #3b82f6;
        }

        .metadata-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .metadata-value {
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
        }

        /* Progress Bar */
        .progress-container {
            background: #f1f5f9;
            border-radius: 50px;
            height: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
        }

        /* Team & Departments */
        .team-grid {
    display: flex;
    flex-wrap: wrap;  /* Permite que los elementos se acomoden en varias filas */
    gap: 20px;
    margin-bottom: 30px;
}

        .team-member div {
        font-size: 14px;
    }

    .team-member {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #f8fafc;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    flex-shrink: 0;  /* Evita que los elementos se compriman */
    max-width: 200px; /* Ajusta el ancho máximo para que los elementos no se estiren demasiado */
    width: 100%; /* Permite que los elementos ocupen el ancho completo disponible */
}

.member-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 16px;
}

        /* Tasks Section */
        .task-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .task-card {
            padding: 15px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .task-card.todo { border-color: #3b82f6; }
        .task-card.progress { border-color: #f59e0b; }
        .task-card.review { border-color: #8b5cf6; }
        .task-card.done { border-color: #10b981; }

        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .task-title {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .data-table th {
            background: #f8fafc;
            padding: 10px;
            text-align: left;
            color: #64748b;
            font-size: 12px;
        }

        .data-table td {
            padding: 12px;
            font-size: 14px;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Footer */
        .report-footer {
            text-align: center;
            padding: 10px;
            color: #64748b;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            margin-top: 30px;
        }
        .report-header {
        text-align: center;
        padding: 30px 15px;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: black;
    
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .report-header h1 {
        font-size: 36px;
        font-weight: 700;
        letter-spacing: -1px;
        margin-bottom: 10px;
    }

    .report-header p {
        font-size: 16px;
        font-weight: 400;
        opacity: 0.8;
    }

    /* Add some spacing between the lines and make them smaller for a modern look */
    .report-header p {
        margin-top: 5px;
        font-size: 14px;
        line-height: 1.5;
        opacity: 0.85;
    }
        /* Print Styles */
        @media print {
            .container {
                padding: 10px;
            }
            .report-header {
                padding: 15px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header class="report-header">
            <h1>{{ $project->name }}</h1>
            <p>Project Report • Generated on {{ date('F j, Y') }}</p>
        </header>

        <!-- Project Overview Section -->
        <section class="report-section">
            <h2 class="section-title">Project Overview</h2>
            <div class="metadata-grid">
                <div class="metadata-card">
                    <div class="metadata-label">Status</div>
                    <div class="metadata-value">
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                </div>
                <div class="metadata-card">
                    <div class="metadata-label">Priority</div>
                    <div class="metadata-value">
                        <div class="priority-indicator">
                            <div class="priority-dot {{ $priorityClass }}"></div>
                            {{ $priorityLabel }}
                        </div>
                    </div>
                </div>
                <div class="metadata-card">
                    <div class="metadata-label">Timeline</div>
                    <div class="metadata-value">
                        {{ date('M j', strtotime($project->start_date)) }} - {{ date('M j, Y', strtotime($project->end_date)) }}
                    </div>
                </div>
                <div class="metadata-card">
                    <div class="metadata-label">Progress</div>
                    <div class="metadata-value">
                        {{ round(($completedTasks / max(1, $totalTasks)) * 100) }}% Completed
                    </div>
                </div>
            </div>

            <!-- Progress Visualization -->
            <div class="progress-container">
                <div class="progress-fill" style="width: {{ ($completedTasks / max(1, $totalTasks)) * 100 }}%"></div>
            </div>
        </section>

        <!-- Departments and Team Members Section -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <section class="report-section">
                <h2 class="section-title">Departments</h2>
                <table class="data-table">
                    <tbody>
                        @foreach($project->departments as $department)
                            <tr>
                                <td>{{ $department->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            <section class="report-section">
            
    <h2 class="section-title">Team Members</h2>
    <div class="team-grid">
        @foreach($project->users as $user)
        <div class="team-member">
            <div class="member-avatar" style="background-color: #{{ substr(md5($user->name), 0, 6) }};">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <div style="font-weight: 600;">{{ $user->name }}</div>
                <div style="font-size: 12px; color: #64748b;">{{ $user->role ?? 'Team Member' }}</div>
            </div>
        </div>
        @endforeach
    </div>
</section>
        </div>

        <!-- Tasks Overview Section -->
        <section class="report-section">
            <h2 class="section-title">Task Overview</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Count</th>
                        <th>Completion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['to_do', 'in_progress', 'review', 'done'] as $status)
                    <tr>
                        <td><span class="status-badge {{ $status }}">{{ ucfirst($status) }}</span></td>
                        <td>{{ is_array($tasksByStatus[$status]) || $tasksByStatus[$status] instanceof Countable ? count($tasksByStatus[$status]) : 0 }}</td>

                        <td>
                            @if($status === 'done')
                                100%
                            @else
                                {{ round(count($tasksByStatus[$status]) / max(1, $totalTasks)) * 100 }}%
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <!-- Task Details Section -->
        <div class="page-break"></div>
        <section class="report-section">
            <h2 class="section-title">Task Details</h2>
            <div class="task-grid">
                @foreach($project->tasks as $task)
                <div class="task-card {{ explode('_', $task->status)[0] }}">
                    <div class="task-header">
                        <div class="task-title">{{ $task->name }}</div>
                    </div>
                    <table class="data-table">
                        <tr>
                            <td>Status</td>
                            <td><span class="status-badge {{ $task->statusClass }}">{{ $task->statusLabel }}</span></td>
                        </tr>
                        <tr>
                            <td>Assigned To</td>
                            <td>{{ $task->user ? $task->user->name : 'Unassigned' }}</td>
                        </tr>
                        <tr>
                            <td>Timeline</td>
                            <td>{{ date('M j', strtotime($task->start_date)) }} - {{ date('M j', strtotime($task->end_date)) }}</td>
                        </tr>
                        <tr>
                            <td>Hours</td>
                            <td>{{ $task->completed_hours ?? 0 }}/{{ $task->estimated_hours }}h</td>
                        </tr>
                    </table>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Footer Section -->
        <footer class="report-footer">
            <p>{{ config('app.name') }} • {{ date('Y') }} • Page 1 of <span id="pageNumber"></span></p>
        </footer>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pageCount = Math.ceil(document.querySelectorAll('.task-card').length / 5) + 1;
            document.getElementById('pageNumber').textContent = pageCount;
        });
    </script>
</body>
</html>
