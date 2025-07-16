import React, { useState } from 'react'
import { Plus, Search, Filter, CheckCircle2, Circle, Calendar, Flag, Trash2 } from 'lucide-react'
import { useLocalStorage } from '../hooks/useLocalStorage'
import { Task } from '../types'

const TaskManager: React.FC = () => {
  const [tasks, setTasks] = useLocalStorage<Task[]>('tasks', [])
  const [searchTerm, setSearchTerm] = useState('')
  const [filterStatus, setFilterStatus] = useState<'all' | 'active' | 'completed'>('all')
  const [filterPriority, setFilterPriority] = useState<'all' | 'high' | 'medium' | 'low'>('all')
  const [showAddForm, setShowAddForm] = useState(false)
  const [newTask, setNewTask] = useState({
    title: '',
    description: '',
    priority: 'medium' as const,
    dueDate: '',
    category: ''
  })

  const addTask = () => {
    if (!newTask.title.trim()) return

    const task: Task = {
      id: Date.now().toString(),
      title: newTask.title,
      description: newTask.description,
      completed: false,
      priority: newTask.priority,
      dueDate: newTask.dueDate || undefined,
      category: newTask.category || undefined,
      createdAt: new Date().toISOString()
    }

    setTasks([task, ...tasks])
    setNewTask({ title: '', description: '', priority: 'medium', dueDate: '', category: '' })
    setShowAddForm(false)
  }

  const toggleTask = (id: string) => {
    setTasks(tasks.map(task => 
      task.id === id ? { ...task, completed: !task.completed } : task
    ))
  }

  const deleteTask = (id: string) => {
    setTasks(tasks.filter(task => task.id !== id))
  }

  const filteredTasks = tasks.filter(task => {
    const matchesSearch = task.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         task.description?.toLowerCase().includes(searchTerm.toLowerCase())
    const matchesStatus = filterStatus === 'all' || 
                         (filterStatus === 'active' && !task.completed) ||
                         (filterStatus === 'completed' && task.completed)
    const matchesPriority = filterPriority === 'all' || task.priority === filterPriority

    return matchesSearch && matchesStatus && matchesPriority
  })

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'high': return 'text-ios-red'
      case 'medium': return 'text-ios-orange'
      case 'low': return 'text-ios-green'
      default: return 'text-ios-gray-500'
    }
  }

  const completedCount = tasks.filter(task => task.completed).length
  const totalCount = tasks.length

  return (
    <div className="p-4 space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-ios-gray-900">Tasks</h1>
          <p className="text-ios-gray-500">
            {completedCount} of {totalCount} completed
          </p>
        </div>
        <button
          onClick={() => setShowAddForm(true)}
          className="ios-button-primary"
        >
          <Plus size={20} />
        </button>
      </div>

      {/* Search and Filters */}
      <div className="space-y-3">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-ios-gray-400" size={20} />
          <input
            type="text"
            placeholder="Search tasks..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="ios-input pl-10"
          />
        </div>

        <div className="flex space-x-2">
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value as any)}
            className="ios-input flex-1"
          >
            <option value="all">All Tasks</option>
            <option value="active">Active</option>
            <option value="completed">Completed</option>
          </select>
          <select
            value={filterPriority}
            onChange={(e) => setFilterPriority(e.target.value as any)}
            className="ios-input flex-1"
          >
            <option value="all">All Priorities</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>
        </div>
      </div>

      {/* Add Task Form */}
      {showAddForm && (
        <div className="ios-card p-4 space-y-3 animate-slide-up">
          <h3 className="font-semibold text-ios-gray-900">Add New Task</h3>
          <input
            type="text"
            placeholder="Task title"
            value={newTask.title}
            onChange={(e) => setNewTask({ ...newTask, title: e.target.value })}
            className="ios-input"
          />
          <textarea
            placeholder="Description (optional)"
            value={newTask.description}
            onChange={(e) => setNewTask({ ...newTask, description: e.target.value })}
            className="ios-input resize-none"
            rows={3}
          />
          <div className="grid grid-cols-2 gap-3">
            <select
              value={newTask.priority}
              onChange={(e) => setNewTask({ ...newTask, priority: e.target.value as any })}
              className="ios-input"
            >
              <option value="low">Low Priority</option>
              <option value="medium">Medium Priority</option>
              <option value="high">High Priority</option>
            </select>
            <input
              type="date"
              value={newTask.dueDate}
              onChange={(e) => setNewTask({ ...newTask, dueDate: e.target.value })}
              className="ios-input"
            />
          </div>
          <input
            type="text"
            placeholder="Category (optional)"
            value={newTask.category}
            onChange={(e) => setNewTask({ ...newTask, category: e.target.value })}
            className="ios-input"
          />
          <div className="flex space-x-3">
            <button onClick={addTask} className="ios-button-primary flex-1">
              Add Task
            </button>
            <button onClick={() => setShowAddForm(false)} className="ios-button-secondary flex-1">
              Cancel
            </button>
          </div>
        </div>
      )}

      {/* Tasks List */}
      <div className="space-y-3">
        {filteredTasks.length === 0 ? (
          <div className="text-center py-8 text-ios-gray-500">
            <CheckCircle2 size={48} className="mx-auto mb-3 opacity-50" />
            <p>No tasks found</p>
          </div>
        ) : (
          filteredTasks.map((task) => (
            <div
              key={task.id}
              className={`ios-card p-4 transition-all duration-200 ${
                task.completed ? 'opacity-60' : ''
              }`}
            >
              <div className="flex items-start space-x-3">
                <button
                  onClick={() => toggleTask(task.id)}
                  className="mt-1 transition-colors duration-200"
                >
                  {task.completed ? (
                    <CheckCircle2 className="text-ios-green" size={24} />
                  ) : (
                    <Circle className="text-ios-gray-400" size={24} />
                  )}
                </button>
                
                <div className="flex-1 min-w-0">
                  <h3 className={`font-medium ${
                    task.completed ? 'line-through text-ios-gray-500' : 'text-ios-gray-900'
                  }`}>
                    {task.title}
                  </h3>
                  {task.description && (
                    <p className="text-ios-gray-600 text-sm mt-1">{task.description}</p>
                  )}
                  
                  <div className="flex items-center space-x-4 mt-2 text-xs">
                    <div className={`flex items-center space-x-1 ${getPriorityColor(task.priority)}`}>
                      <Flag size={12} />
                      <span className="capitalize">{task.priority}</span>
                    </div>
                    {task.dueDate && (
                      <div className="flex items-center space-x-1 text-ios-gray-500">
                        <Calendar size={12} />
                        <span>{new Date(task.dueDate).toLocaleDateString()}</span>
                      </div>
                    )}
                    {task.category && (
                      <span className="bg-ios-gray-100 px-2 py-1 rounded-full text-ios-gray-600">
                        {task.category}
                      </span>
                    )}
                  </div>
                </div>
                
                <button
                  onClick={() => deleteTask(task.id)}
                  className="text-ios-red p-1 transition-colors duration-200"
                >
                  <Trash2 size={18} />
                </button>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  )
}

export default TaskManager