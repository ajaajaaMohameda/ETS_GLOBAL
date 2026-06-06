'use client';

import { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { sessionService } from '@/services/session.service';
import { reservationService } from '@/services/reservation.service';
import { Session, PaginatedResponse } from '@/types';

export default function SessionsPage() {
  const router = useRouter();
  const [data, setData] = useState<PaginatedResponse<Session> | null>(null);
  const [page, setPage] = useState(1);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');
  const [isReserving, setIsReserving] = useState<string | null>(null);

  const fetchSessions = async (currentPage: number) => {
    setIsLoading(true);
    setError('');
    try {
      const result = await sessionService.getSessions(currentPage, 6);
      setData(result);
    } catch (err: any) {
      setError('Erreur lors du chargement des sessions disponibles.');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchSessions(page);
  }, [page]);

  const handleReserve = async (sessionId: string) => {
    setIsReserving(sessionId);
    setError('');
    
    try {
      await reservationService.createReservation(sessionId);
      router.push('/reservations');
    } catch (err: any) {
      setError(err.response?.data?.error || 'Erreur lors de la réservation. Veuillez réessayer.');
      setIsReserving(null);
    }
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  if (isLoading && !data) {
    return <div className="flex justify-center items-center h-64">Chargement des sessions...</div>;
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-gray-900">Sessions de tests disponibles</h1>
      </div>

      {error && (
        <div className="bg-red-50 text-red-500 p-4 rounded-md">
          {error}
        </div>
      )}

      <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {data?.data.map((session) => (
          <div key={session.id} className="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 flex flex-col">
            <div className="p-5 flex-grow">
              <div className="flex items-center justify-between mb-4">
                <span className="px-3 py-1 text-sm font-semibold text-blue-800 bg-blue-100 rounded-full">
                  {session.language}
                </span>
                <span className="text-sm font-medium text-gray-500">
                  {session.capacity} places
                </span>
              </div>
              <p className="text-gray-900 font-medium mb-2">{formatDate(session.startsAt)}</p>
              <p className="text-gray-500 text-sm">{session.location}</p>
            </div>
            <div className="bg-gray-50 px-5 py-3 border-t border-gray-200">
              <button
                onClick={() => handleReserve(session.id)}
                disabled={session.capacity <= 0 || isReserving === session.id}
                className="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed"
              >
                {isReserving === session.id ? 'Réservation...' : session.capacity > 0 ? 'Réserver cette session' : 'Complet'}
              </button>
            </div>
          </div>
        ))}
      </div>

      {data && data.pagination.pages > 1 && (
        <div className="flex items-center justify-between bg-white px-4 py-3 border-t border-gray-200 sm:px-6 rounded-lg shadow-sm mt-6">
          <div className="flex justify-between w-full">
            <button
              onClick={() => setPage(p => Math.max(1, p - 1))}
              disabled={page === 1}
              className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
            >
              Précédent
            </button>
            <span className="text-sm text-gray-700 self-center">
              Page <span className="font-medium">{data.pagination.page}</span> sur <span className="font-medium">{data.pagination.pages}</span>
            </span>
            <button
              onClick={() => setPage(p => Math.min(data.pagination.pages, p + 1))}
              disabled={page === data.pagination.pages}
              className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
            >
              Suivant
            </button>
          </div>
        </div>
      )}
    </div>
  );
}