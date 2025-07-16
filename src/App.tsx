import React, { useState, useEffect } from 'react'
import { Dumbbell, Film, CheckSquare, DollarSign } from 'lucide-react'
import WorkoutTracker from './components/WorkoutTracker'
import Watchlist from './components/Watchlist'
import TaskManager from './components/TaskManager'
import ExpenseTracker from './components/ExpenseTracker'

type Tab = 'workouts' | 'watchlist' | 'tasks' | 'expenses'

function App() {
  const [activeTab, setActiveTab] = useState<Tab>('tasks')

  useEffect(() => {
    // Register service worker for PWA functionality
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js')
    }
  }, [])

  const tabs = [
    { id: 'tasks' as Tab, label: 'Tasks', icon: CheckSquare, color: 'text-ios-blue' },
    { id: 'workouts' as Tab, label: 'Workouts', icon: Dumbbell, color: 'text-ios-green' },
    { id: 'watchlist' as Tab, label: 'Watchlist', icon: Film, color: 'text-ios-purple' },
    { id: 'expenses' as Tab, label: 'Expenses', icon: DollarSign, color: 'text-ios-orange' }
  ]

  const renderActiveTab = () => {
    switch (activeTab) {
      case 'workouts':
        return <WorkoutTracker />
      case 'watchlist':
        return <Watchlist />
      case 'tasks':
        return <TaskManager />
      case 'expenses':
        return <ExpenseTracker />
      default:
        return <TaskManager />
    }
  }

  return (
    <div className="min-h-screen bg-ios-gray-50 pb-20">
      <main className="safe-area-pt">
        {renderActiveTab()}
      </main>

      {/* Tab Bar */}
      <nav className="tab-bar">
        <div className="flex">
          {tabs.map((tab) => {
            const Icon = tab.icon
            const isActive = activeTab === tab.id
            
            return (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`tab-item ${isActive ? 'active' : ''}`}
              >
                <Icon 
                  size={24} 
                  className={`mb-1 transition-all duration-200 ${
                    isActive ? tab.color : 'text-ios-gray-500'
                  }`}
                />
                <span className="text-xs font-medium">{tab.label}</span>
              </button>
            )
          })}
        </div>
      </nav>
    </div>
  )
}

export default App