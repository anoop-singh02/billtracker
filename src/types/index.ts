export interface Task {
  id: string
  title: string
  description?: string
  completed: boolean
  priority: 'low' | 'medium' | 'high'
  dueDate?: string
  createdAt: string
  category?: string
}

export interface Workout {
  id: string
  name: string
  date: string
  duration: number // in minutes
  exercises: Exercise[]
  notes?: string
}

export interface Exercise {
  id: string
  name: string
  sets: Set[]
  category: string
}

export interface Set {
  reps: number
  weight?: number
  duration?: number // for time-based exercises
  distance?: number // for cardio
}

export interface WatchlistItem {
  id: string
  title: string
  type: 'movie' | 'tv' | 'book'
  status: 'want-to-watch' | 'watching' | 'completed'
  rating?: number
  notes?: string
  dateAdded: string
  dateCompleted?: string
  genre?: string
  year?: number
}

export interface Expense {
  id: string
  amount: number
  description: string
  category: string
  date: string
  type: 'expense' | 'income'
  tags?: string[]
}