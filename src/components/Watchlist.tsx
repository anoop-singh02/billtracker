import React, { useState } from 'react'
import { Plus, Search, Star, Calendar, Trash2, Eye, Clock, CheckCircle } from 'lucide-react'
import { useLocalStorage } from '../hooks/useLocalStorage'
import { WatchlistItem } from '../types'

const Watchlist: React.FC = () => {
  const [items, setItems] = useLocalStorage<WatchlistItem[]>('watchlist', [])
  const [searchTerm, setSearchTerm] = useState('')
  const [filterType, setFilterType] = useState<'all' | 'movie' | 'tv' | 'book'>('all')
  const [filterStatus, setFilterStatus] = useState<'all' | 'want-to-watch' | 'watching' | 'completed'>('all')
  const [showAddForm, setShowAddForm] = useState(false)
  const [newItem, setNewItem] = useState({
    title: '',
    type: 'movie' as const,
    genre: '',
    year: '',
    notes: ''
  })

  const addItem = () => {
    if (!newItem.title.trim()) return

    const item: WatchlistItem = {
      id: Date.now().toString(),
      title: newItem.title,
      type: newItem.type,
      status: 'want-to-watch',
      dateAdded: new Date().toISOString(),
      genre: newItem.genre || undefined,
      year: newItem.year ? parseInt(newItem.year) : undefined,
      notes: newItem.notes || undefined
    }

    setItems([item, ...items])
    setNewItem({ title: '', type: 'movie', genre: '', year: '', notes: '' })
    setShowAddForm(false)
  }

  const updateStatus = (id: string, status: WatchlistItem['status']) => {
    setItems(items.map(item => 
      item.id === id 
        ? { 
            ...item, 
            status,
            dateCompleted: status === 'completed' ? new Date().toISOString() : undefined
          }
        : item
    ))
  }

  const updateRating = (id: string, rating: number) => {
    setItems(items.map(item => 
      item.id === id ? { ...item, rating } : item
    ))
  }

  const deleteItem = (id: string) => {
    setItems(items.filter(item => item.id !== id))
  }

  const filteredItems = items.filter(item => {
    const matchesSearch = item.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         item.genre?.toLowerCase().includes(searchTerm.toLowerCase())
    const matchesType = filterType === 'all' || item.type === filterType
    const matchesStatus = filterStatus === 'all' || item.status === filterStatus

    return matchesSearch && matchesType && matchesStatus
  })

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'want-to-watch': return <Clock size={16} className="text-ios-orange" />
      case 'watching': return <Eye size={16} className="text-ios-blue" />
      case 'completed': return <CheckCircle size={16} className="text-ios-green" />
      default: return null
    }
  }

  const getTypeEmoji = (type: string) => {
    switch (type) {
      case 'movie': return '🎬'
      case 'tv': return '📺'
      case 'book': return '📚'
      default: return '🎬'
    }
  }

  const completedCount = items.filter(item => item.status === 'completed').length
  const watchingCount = items.filter(item => item.status === 'watching').length

  return (
    <div className="p-4 space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-ios-gray-900">Watchlist</h1>
          <p className="text-ios-gray-500">
            {completedCount} completed • {watchingCount} watching
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
            placeholder="Search titles..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="ios-input pl-10"
          />
        </div>

        <div className="flex space-x-2">
          <select
            value={filterType}
            onChange={(e) => setFilterType(e.target.value as any)}
            className="ios-input flex-1"
          >
            <option value="all">All Types</option>
            <option value="movie">Movies</option>
            <option value="tv">TV Shows</option>
            <option value="book">Books</option>
          </select>
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value as any)}
            className="ios-input flex-1"
          >
            <option value="all">All Status</option>
            <option value="want-to-watch">Want to Watch</option>
            <option value="watching">Watching</option>
            <option value="completed">Completed</option>
          </select>
        </div>
      </div>

      {/* Add Item Form */}
      {showAddForm && (
        <div className="ios-card p-4 space-y-3 animate-slide-up">
          <h3 className="font-semibold text-ios-gray-900">Add to Watchlist</h3>
          <input
            type="text"
            placeholder="Title"
            value={newItem.title}
            onChange={(e) => setNewItem({ ...newItem, title: e.target.value })}
            className="ios-input"
          />
          <div className="grid grid-cols-2 gap-3">
            <select
              value={newItem.type}
              onChange={(e) => setNewItem({ ...newItem, type: e.target.value as any })}
              className="ios-input"
            >
              <option value="movie">Movie</option>
              <option value="tv">TV Show</option>
              <option value="book">Book</option>
            </select>
            <input
              type="number"
              placeholder="Year"
              value={newItem.year}
              onChange={(e) => setNewItem({ ...newItem, year: e.target.value })}
              className="ios-input"
            />
          </div>
          <input
            type="text"
            placeholder="Genre (optional)"
            value={newItem.genre}
            onChange={(e) => setNewItem({ ...newItem, genre: e.target.value })}
            className="ios-input"
          />
          <textarea
            placeholder="Notes (optional)"
            value={newItem.notes}
            onChange={(e) => setNewItem({ ...newItem, notes: e.target.value })}
            className="ios-input resize-none"
            rows={3}
          />
          <div className="flex space-x-3">
            <button onClick={addItem} className="ios-button-primary flex-1">
              Add to List
            </button>
            <button onClick={() => setShowAddForm(false)} className="ios-button-secondary flex-1">
              Cancel
            </button>
          </div>
        </div>
      )}

      {/* Items List */}
      <div className="space-y-3">
        {filteredItems.length === 0 ? (
          <div className="text-center py-8 text-ios-gray-500">
            <Star size={48} className="mx-auto mb-3 opacity-50" />
            <p>No items found</p>
          </div>
        ) : (
          filteredItems.map((item) => (
            <div key={item.id} className="ios-card p-4">
              <div className="flex items-start justify-between">
                <div className="flex-1">
                  <div className="flex items-center space-x-2 mb-1">
                    <span className="text-lg">{getTypeEmoji(item.type)}</span>
                    <h3 className="font-medium text-ios-gray-900">{item.title}</h3>
                    {item.year && (
                      <span className="text-sm text-ios-gray-500">({item.year})</span>
                    )}
                  </div>
                  
                  <div className="flex items-center space-x-3 mb-2">
                    <div className="flex items-center space-x-1">
                      {getStatusIcon(item.status)}
                      <span className="text-sm text-ios-gray-600 capitalize">
                        {item.status.replace('-', ' ')}
                      </span>
                    </div>
                    {item.genre && (
                      <span className="text-sm bg-ios-gray-100 px-2 py-1 rounded-full text-ios-gray-600">
                        {item.genre}
                      </span>
                    )}
                  </div>

                  {item.status === 'completed' && (
                    <div className="flex items-center space-x-1 mb-2">
                      {[1, 2, 3, 4, 5].map((star) => (
                        <button
                          key={star}
                          onClick={() => updateRating(item.id, star)}
                          className="transition-colors duration-200"
                        >
                          <Star
                            size={16}
                            className={star <= (item.rating || 0) ? 'text-ios-orange fill-current' : 'text-ios-gray-300'}
                          />
                        </button>
                      ))}
                    </div>
                  )}

                  {item.notes && (
                    <p className="text-sm text-ios-gray-600 mb-2">{item.notes}</p>
                  )}

                  <div className="flex space-x-2">
                    <select
                      value={item.status}
                      onChange={(e) => updateStatus(item.id, e.target.value as any)}
                      className="text-sm border border-ios-gray-200 rounded-lg px-2 py-1"
                    >
                      <option value="want-to-watch">Want to Watch</option>
                      <option value="watching">Watching</option>
                      <option value="completed">Completed</option>
                    </select>
                  </div>
                </div>
                
                <button
                  onClick={() => deleteItem(item.id)}
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

export default Watchlist