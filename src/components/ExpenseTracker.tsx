import React, { useState } from 'react'
import { Plus, Search, TrendingUp, TrendingDown, Calendar, Trash2, DollarSign } from 'lucide-react'
import { useLocalStorage } from '../hooks/useLocalStorage'
import { Expense } from '../types'

const ExpenseTracker: React.FC = () => {
  const [expenses, setExpenses] = useLocalStorage<Expense[]>('expenses', [])
  const [searchTerm, setSearchTerm] = useState('')
  const [filterCategory, setFilterCategory] = useState<string>('all')
  const [filterType, setFilterType] = useState<'all' | 'expense' | 'income'>('all')
  const [showAddForm, setShowAddForm] = useState(false)
  const [newExpense, setNewExpense] = useState({
    amount: '',
    description: '',
    category: '',
    type: 'expense' as const,
    date: new Date().toISOString().split('T')[0]
  })

  const addExpense = () => {
    if (!newExpense.amount || !newExpense.description) return

    const expense: Expense = {
      id: Date.now().toString(),
      amount: parseFloat(newExpense.amount),
      description: newExpense.description,
      category: newExpense.category || 'Other',
      type: newExpense.type,
      date: newExpense.date,
      tags: []
    }

    setExpenses([expense, ...expenses])
    setNewExpense({
      amount: '',
      description: '',
      category: '',
      type: 'expense',
      date: new Date().toISOString().split('T')[0]
    })
    setShowAddForm(false)
  }

  const deleteExpense = (id: string) => {
    setExpenses(expenses.filter(expense => expense.id !== id))
  }

  const filteredExpenses = expenses.filter(expense => {
    const matchesSearch = expense.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         expense.category.toLowerCase().includes(searchTerm.toLowerCase())
    const matchesCategory = filterCategory === 'all' || expense.category === filterCategory
    const matchesType = filterType === 'all' || expense.type === filterType

    return matchesSearch && matchesCategory && matchesType
  })

  // Calculate statistics
  const thisMonth = new Date()
  thisMonth.setDate(1)
  
  const thisMonthExpenses = expenses.filter(expense => 
    new Date(expense.date) >= thisMonth && expense.type === 'expense'
  )
  const thisMonthIncome = expenses.filter(expense => 
    new Date(expense.date) >= thisMonth && expense.type === 'income'
  )

  const totalExpenses = thisMonthExpenses.reduce((sum, expense) => sum + expense.amount, 0)
  const totalIncome = thisMonthIncome.reduce((sum, expense) => sum + expense.amount, 0)
  const netAmount = totalIncome - totalExpenses

  // Get categories
  const categories = Array.from(new Set(expenses.map(expense => expense.category)))

  // Category breakdown
  const categoryTotals = expenses.reduce((acc, expense) => {
    if (expense.type === 'expense') {
      acc[expense.category] = (acc[expense.category] || 0) + expense.amount
    }
    return acc
  }, {} as Record<string, number>)

  const sortedCategories = Object.entries(categoryTotals)
    .sort(([,a], [,b]) => b - a)
    .slice(0, 5)

  return (
    <div className="p-4 space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-ios-gray-900">Expenses</h1>
          <p className="text-ios-gray-500">
            {expenses.length} transactions this month
          </p>
        </div>
        <button
          onClick={() => setShowAddForm(true)}
          className="ios-button-primary"
        >
          <Plus size={20} />
        </button>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-3 gap-3">
        <div className="ios-card p-3 text-center">
          <div className="flex items-center justify-center mb-1">
            <TrendingDown className="text-ios-red" size={20} />
          </div>
          <p className="text-xs text-ios-gray-500">Expenses</p>
          <p className="font-bold text-ios-red">${totalExpenses.toFixed(2)}</p>
        </div>
        <div className="ios-card p-3 text-center">
          <div className="flex items-center justify-center mb-1">
            <TrendingUp className="text-ios-green" size={20} />
          </div>
          <p className="text-xs text-ios-gray-500">Income</p>
          <p className="font-bold text-ios-green">${totalIncome.toFixed(2)}</p>
        </div>
        <div className="ios-card p-3 text-center">
          <div className="flex items-center justify-center mb-1">
            <DollarSign className={netAmount >= 0 ? 'text-ios-green' : 'text-ios-red'} size={20} />
          </div>
          <p className="text-xs text-ios-gray-500">Net</p>
          <p className={`font-bold ${netAmount >= 0 ? 'text-ios-green' : 'text-ios-red'}`}>
            ${Math.abs(netAmount).toFixed(2)}
          </p>
        </div>
      </div>

      {/* Top Categories */}
      {sortedCategories.length > 0 && (
        <div className="ios-card p-4">
          <h3 className="font-semibold text-ios-gray-900 mb-3">Top Categories</h3>
          <div className="space-y-2">
            {sortedCategories.map(([category, amount]) => (
              <div key={category} className="flex items-center justify-between">
                <span className="text-sm text-ios-gray-700">{category}</span>
                <span className="text-sm font-medium text-ios-gray-900">
                  ${amount.toFixed(2)}
                </span>
              </div>
            ))}
          </div>
        </div>
      )}

      {/* Search and Filters */}
      <div className="space-y-3">
        <div className="relative">
          <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-ios-gray-400" size={20} />
          <input
            type="text"
            placeholder="Search transactions..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="ios-input pl-10"
          />
        </div>

        <div className="flex space-x-2">
          <select
            value={filterCategory}
            onChange={(e) => setFilterCategory(e.target.value)}
            className="ios-input flex-1"
          >
            <option value="all">All Categories</option>
            {categories.map(category => (
              <option key={category} value={category}>{category}</option>
            ))}
          </select>
          <select
            value={filterType}
            onChange={(e) => setFilterType(e.target.value as any)}
            className="ios-input flex-1"
          >
            <option value="all">All Types</option>
            <option value="expense">Expenses</option>
            <option value="income">Income</option>
          </select>
        </div>
      </div>

      {/* Add Expense Form */}
      {showAddForm && (
        <div className="ios-card p-4 space-y-3 animate-slide-up">
          <h3 className="font-semibold text-ios-gray-900">Add Transaction</h3>
          <div className="grid grid-cols-2 gap-3">
            <input
              type="number"
              placeholder="Amount"
              value={newExpense.amount}
              onChange={(e) => setNewExpense({ ...newExpense, amount: e.target.value })}
              className="ios-input"
              step="0.01"
            />
            <select
              value={newExpense.type}
              onChange={(e) => setNewExpense({ ...newExpense, type: e.target.value as any })}
              className="ios-input"
            >
              <option value="expense">Expense</option>
              <option value="income">Income</option>
            </select>
          </div>
          <input
            type="text"
            placeholder="Description"
            value={newExpense.description}
            onChange={(e) => setNewExpense({ ...newExpense, description: e.target.value })}
            className="ios-input"
          />
          <div className="grid grid-cols-2 gap-3">
            <input
              type="text"
              placeholder="Category"
              value={newExpense.category}
              onChange={(e) => setNewExpense({ ...newExpense, category: e.target.value })}
              className="ios-input"
            />
            <input
              type="date"
              value={newExpense.date}
              onChange={(e) => setNewExpense({ ...newExpense, date: e.target.value })}
              className="ios-input"
            />
          </div>
          <div className="flex space-x-3">
            <button onClick={addExpense} className="ios-button-primary flex-1">
              Add Transaction
            </button>
            <button onClick={() => setShowAddForm(false)} className="ios-button-secondary flex-1">
              Cancel
            </button>
          </div>
        </div>
      )}

      {/* Expenses List */}
      <div className="space-y-3">
        {filteredExpenses.length === 0 ? (
          <div className="text-center py-8 text-ios-gray-500">
            <DollarSign size={48} className="mx-auto mb-3 opacity-50" />
            <p>No transactions found</p>
          </div>
        ) : (
          filteredExpenses.map((expense) => (
            <div key={expense.id} className="ios-card p-4">
              <div className="flex items-start justify-between">
                <div className="flex-1">
                  <div className="flex items-center space-x-2 mb-1">
                    {expense.type === 'expense' ? (
                      <TrendingDown className="text-ios-red" size={16} />
                    ) : (
                      <TrendingUp className="text-ios-green" size={16} />
                    )}
                    <h3 className="font-medium text-ios-gray-900">{expense.description}</h3>
                  </div>
                  
                  <div className="flex items-center space-x-3 text-sm text-ios-gray-600">
                    <span className="bg-ios-gray-100 px-2 py-1 rounded-full">
                      {expense.category}
                    </span>
                    <div className="flex items-center space-x-1">
                      <Calendar size={12} />
                      <span>{new Date(expense.date).toLocaleDateString()}</span>
                    </div>
                  </div>
                </div>
                
                <div className="flex items-center space-x-2">
                  <span className={`font-bold ${
                    expense.type === 'expense' ? 'text-ios-red' : 'text-ios-green'
                  }`}>
                    {expense.type === 'expense' ? '-' : '+'}${expense.amount.toFixed(2)}
                  </span>
                  <button
                    onClick={() => deleteExpense(expense.id)}
                    className="text-ios-red p-1 transition-colors duration-200"
                  >
                    <Trash2 size={16} />
                  </button>
                </div>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  )
}

export default ExpenseTracker