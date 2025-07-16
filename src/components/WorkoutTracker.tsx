import React, { useState } from 'react'
import { Plus, Play, Clock, Calendar, Trash2, Edit3 } from 'lucide-react'
import { useLocalStorage } from '../hooks/useLocalStorage'
import { Workout, Exercise } from '../types'

const WorkoutTracker: React.FC = () => {
  const [workouts, setWorkouts] = useLocalStorage<Workout[]>('workouts', [])
  const [showAddForm, setShowAddForm] = useState(false)
  const [activeWorkout, setActiveWorkout] = useState<Workout | null>(null)
  const [newWorkout, setNewWorkout] = useState({
    name: '',
    exercises: [] as Exercise[],
    notes: ''
  })

  const startWorkout = () => {
    const workout: Workout = {
      id: Date.now().toString(),
      name: newWorkout.name || 'Quick Workout',
      date: new Date().toISOString(),
      duration: 0,
      exercises: [],
      notes: newWorkout.notes
    }
    setActiveWorkout(workout)
    setShowAddForm(false)
  }

  const finishWorkout = (duration: number) => {
    if (activeWorkout) {
      const completedWorkout = { ...activeWorkout, duration }
      setWorkouts([completedWorkout, ...workouts])
      setActiveWorkout(null)
    }
  }

  const deleteWorkout = (id: string) => {
    setWorkouts(workouts.filter(w => w.id !== id))
  }

  const formatDuration = (minutes: number) => {
    const hours = Math.floor(minutes / 60)
    const mins = minutes % 60
    return hours > 0 ? `${hours}h ${mins}m` : `${mins}m`
  }

  const thisWeekWorkouts = workouts.filter(workout => {
    const workoutDate = new Date(workout.date)
    const weekAgo = new Date()
    weekAgo.setDate(weekAgo.getDate() - 7)
    return workoutDate >= weekAgo
  })

  const totalMinutesThisWeek = thisWeekWorkouts.reduce((sum, workout) => sum + workout.duration, 0)

  if (activeWorkout) {
    return <ActiveWorkout workout={activeWorkout} onFinish={finishWorkout} />
  }

  return (
    <div className="p-4 space-y-4">
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-ios-gray-900">Workouts</h1>
          <p className="text-ios-gray-500">
            {thisWeekWorkouts.length} workouts this week • {formatDuration(totalMinutesThisWeek)}
          </p>
        </div>
        <button
          onClick={() => setShowAddForm(true)}
          className="ios-button-primary"
        >
          <Plus size={20} />
        </button>
      </div>

      {/* Quick Start */}
      <div className="ios-card p-4">
        <h3 className="font-semibold text-ios-gray-900 mb-3">Quick Start</h3>
        <button
          onClick={startWorkout}
          className="w-full ios-button-primary flex items-center justify-center space-x-2"
        >
          <Play size={20} />
          <span>Start Empty Workout</span>
        </button>
      </div>

      {/* Add Workout Form */}
      {showAddForm && (
        <div className="ios-card p-4 space-y-3 animate-slide-up">
          <h3 className="font-semibold text-ios-gray-900">Plan Workout</h3>
          <input
            type="text"
            placeholder="Workout name"
            value={newWorkout.name}
            onChange={(e) => setNewWorkout({ ...newWorkout, name: e.target.value })}
            className="ios-input"
          />
          <textarea
            placeholder="Notes (optional)"
            value={newWorkout.notes}
            onChange={(e) => setNewWorkout({ ...newWorkout, notes: e.target.value })}
            className="ios-input resize-none"
            rows={3}
          />
          <div className="flex space-x-3">
            <button onClick={startWorkout} className="ios-button-primary flex-1">
              Start Workout
            </button>
            <button onClick={() => setShowAddForm(false)} className="ios-button-secondary flex-1">
              Cancel
            </button>
          </div>
        </div>
      )}

      {/* Recent Workouts */}
      <div className="space-y-3">
        <h3 className="font-semibold text-ios-gray-900">Recent Workouts</h3>
        {workouts.length === 0 ? (
          <div className="text-center py-8 text-ios-gray-500">
            <Play size={48} className="mx-auto mb-3 opacity-50" />
            <p>No workouts yet</p>
            <p className="text-sm">Start your first workout!</p>
          </div>
        ) : (
          workouts.map((workout) => (
            <div key={workout.id} className="ios-card p-4">
              <div className="flex items-start justify-between">
                <div className="flex-1">
                  <h4 className="font-medium text-ios-gray-900">{workout.name}</h4>
                  <div className="flex items-center space-x-4 mt-1 text-sm text-ios-gray-600">
                    <div className="flex items-center space-x-1">
                      <Calendar size={14} />
                      <span>{new Date(workout.date).toLocaleDateString()}</span>
                    </div>
                    <div className="flex items-center space-x-1">
                      <Clock size={14} />
                      <span>{formatDuration(workout.duration)}</span>
                    </div>
                  </div>
                  {workout.exercises.length > 0 && (
                    <p className="text-sm text-ios-gray-500 mt-1">
                      {workout.exercises.length} exercises
                    </p>
                  )}
                  {workout.notes && (
                    <p className="text-sm text-ios-gray-600 mt-2">{workout.notes}</p>
                  )}
                </div>
                <button
                  onClick={() => deleteWorkout(workout.id)}
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

const ActiveWorkout: React.FC<{
  workout: Workout
  onFinish: (duration: number) => void
}> = ({ workout, onFinish }) => {
  const [startTime] = useState(Date.now())
  const [currentTime, setCurrentTime] = useState(Date.now())
  const [exercises, setExercises] = useState<Exercise[]>([])
  const [showAddExercise, setShowAddExercise] = useState(false)
  const [newExercise, setNewExercise] = useState({ name: '', category: 'strength' })

  React.useEffect(() => {
    const timer = setInterval(() => setCurrentTime(Date.now()), 1000)
    return () => clearInterval(timer)
  }, [])

  const duration = Math.floor((currentTime - startTime) / 1000 / 60)

  const addExercise = () => {
    if (!newExercise.name.trim()) return
    
    const exercise: Exercise = {
      id: Date.now().toString(),
      name: newExercise.name,
      sets: [],
      category: newExercise.category
    }
    
    setExercises([...exercises, exercise])
    setNewExercise({ name: '', category: 'strength' })
    setShowAddExercise(false)
  }

  const addSet = (exerciseId: string) => {
    setExercises(exercises.map(ex => 
      ex.id === exerciseId 
        ? { ...ex, sets: [...ex.sets, { reps: 0, weight: 0 }] }
        : ex
    ))
  }

  const handleFinish = () => {
    const updatedWorkout = { ...workout, exercises, duration }
    onFinish(duration)
  }

  return (
    <div className="p-4 space-y-4">
      {/* Header */}
      <div className="text-center">
        <h1 className="text-2xl font-bold text-ios-gray-900">{workout.name}</h1>
        <div className="text-3xl font-bold text-ios-green mt-2">
          {Math.floor(duration / 60)}:{(duration % 60).toString().padStart(2, '0')}
        </div>
        <p className="text-ios-gray-500">Workout in progress</p>
      </div>

      {/* Add Exercise */}
      <div className="ios-card p-4">
        {showAddExercise ? (
          <div className="space-y-3">
            <input
              type="text"
              placeholder="Exercise name"
              value={newExercise.name}
              onChange={(e) => setNewExercise({ ...newExercise, name: e.target.value })}
              className="ios-input"
            />
            <select
              value={newExercise.category}
              onChange={(e) => setNewExercise({ ...newExercise, category: e.target.value })}
              className="ios-input"
            >
              <option value="strength">Strength</option>
              <option value="cardio">Cardio</option>
              <option value="flexibility">Flexibility</option>
            </select>
            <div className="flex space-x-3">
              <button onClick={addExercise} className="ios-button-primary flex-1">
                Add Exercise
              </button>
              <button onClick={() => setShowAddExercise(false)} className="ios-button-secondary flex-1">
                Cancel
              </button>
            </div>
          </div>
        ) : (
          <button
            onClick={() => setShowAddExercise(true)}
            className="w-full ios-button-secondary flex items-center justify-center space-x-2"
          >
            <Plus size={20} />
            <span>Add Exercise</span>
          </button>
        )}
      </div>

      {/* Exercises */}
      <div className="space-y-3">
        {exercises.map((exercise) => (
          <div key={exercise.id} className="ios-card p-4">
            <div className="flex items-center justify-between mb-3">
              <h3 className="font-medium text-ios-gray-900">{exercise.name}</h3>
              <span className="text-xs bg-ios-gray-100 px-2 py-1 rounded-full text-ios-gray-600">
                {exercise.category}
              </span>
            </div>
            
            {exercise.sets.map((set, index) => (
              <div key={index} className="flex items-center space-x-2 mb-2">
                <span className="text-sm text-ios-gray-500 w-8">#{index + 1}</span>
                <input
                  type="number"
                  placeholder="Reps"
                  className="ios-input text-sm flex-1"
                  value={set.reps || ''}
                  onChange={(e) => {
                    const newSets = [...exercise.sets]
                    newSets[index] = { ...set, reps: parseInt(e.target.value) || 0 }
                    setExercises(exercises.map(ex => 
                      ex.id === exercise.id ? { ...ex, sets: newSets } : ex
                    ))
                  }}
                />
                <input
                  type="number"
                  placeholder="Weight"
                  className="ios-input text-sm flex-1"
                  value={set.weight || ''}
                  onChange={(e) => {
                    const newSets = [...exercise.sets]
                    newSets[index] = { ...set, weight: parseInt(e.target.value) || 0 }
                    setExercises(exercises.map(ex => 
                      ex.id === exercise.id ? { ...ex, sets: newSets } : ex
                    ))
                  }}
                />
              </div>
            ))}
            
            <button
              onClick={() => addSet(exercise.id)}
              className="w-full ios-button-secondary text-sm py-2"
            >
              Add Set
            </button>
          </div>
        ))}
      </div>

      {/* Finish Workout */}
      <button
        onClick={handleFinish}
        className="w-full ios-button-primary py-4 text-lg font-semibold"
      >
        Finish Workout
      </button>
    </div>
  )
}

export default WorkoutTracker