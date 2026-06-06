'use client';

import { useState, useEffect } from 'react';
import { reservationService } from '@/services/reservation.service';
import { Reservation, PaginatedResponse } from '@/types';
import Link from 'next/link';

export default function ReservationsPage() {
  const [data, setData] = useState<PaginatedResponse<Reservation> | null>(null);
  const [page, setPage] = useState(1);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState('');
  const [isCancelling, setIsCancelling] = useState<string | null>(null);

  const fetchReservations = async (currentPage: number) => {
    setIsLoading(true);
    setError('');
    try {
      const result = await reservationService.getUserReservations(currentPage, 6);
      setData(result);
    } catch (err: any) {
      setError('Erreur lors du chargement de vos réservations.');
    } finally {
      setIsLoading(false);
    }
  };

  useEffect(() => {
    fetchReservations(page);
  }, [page]);

  const handleCancel = async (reservationId: string) => {
    if (!window.confirm('Voulez-vous vraiment annuler cette réservation ?')) {
      return;
    }

    setIsCancelling(reservationId);
    setError('');
    setSuccess('');

    try {
      await reservationService.cancelReservation(reservationId);
      setSuccess('Réservation annulée avec succès.');
      fetchReservations(page);
    } catch (err: any) {
      setError("Erreur lors de l'annulation. Veuillez réessayer.");
      setIsCancelling(null);
    }
  };

  const formatDate = (dateString: string) => {
    if (!dateString) return '—';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return 'Date invalide';
    return date.toLocaleDateString('fr-FR', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  if (isLoading && !data) {
    return (
        <div className="flex justify-center items-center h-64">
          Chargement de vos réservations...
        </div>
    );
  }

  return (
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-2xl font-bold text-gray-900">Mes réservations</h1>
          <Link
              href="/sessions"
              className="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm font-medium transition-colors"
          >
            Nouvelle réservation
          </Link>
        </div>

        {error && (
            <div className="bg-red-50 text-red-500 p-4 rounded-md">{error}</div>
        )}

        {success && (
            <div className="bg-green-50 text-green-700 p-4 rounded-md">{success}</div>
        )}

        {data?.data.length === 0 ? (
            <div className="bg-white rounded-lg shadow-sm p-8 text-center border border-gray-200">
              <p className="text-gray-500 mb-4">
                Vous n'avez aucune réservation pour le moment.
              </p>
              <Link href="/sessions" className="text-blue-600 font-medium hover:underline">
                Voir les sessions disponibles
              </Link>
            </div>
        ) : (
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {data?.data.map((reservation) => (
                  <div
                      key={reservation.id}
                      className="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 flex flex-col"
                  >
                    <div className="p-5 flex-grow">
                      <div className="flex items-center justify-between mb-4">
                  <span className="px-3 py-1 text-sm font-semibold text-green-800 bg-green-100 rounded-full">
                    Confirmée
                  </span>
                        <span className="text-sm font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">
                    {reservation.language}
                  </span>
                      </div>
                      <p className="text-gray-900 font-medium mb-2">
                        {formatDate(reservation.reservedAt)}
                      </p>
                      <p className="text-gray-500 text-sm mb-4">{reservation.location}</p>
                      <p className="text-xs text-gray-400 border-t pt-2 mt-2">
                        Réservé le{' '}
                        {new Date(reservation.reservedAt).toLocaleDateString('fr-FR')}
                      </p>
                    </div>
                    <div className="bg-gray-50 px-5 py-3 border-t border-gray-200">
                      <button
                          onClick={() => handleCancel(reservation.id)}
                          disabled={isCancelling === reservation.id}
                          className="w-full flex justify-center items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md shadow-sm text-red-700 bg-white hover:bg-red-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors outline-none focus:outline-none select-none"
                      >
                        {isCancelling === reservation.id ? 'Annulation...' : 'Annuler la réservation'}
                      </button>
                    </div>
                  </div>
              ))}
            </div>
        )}

        {data && data.pagination.pages > 1 && (
            <div className="flex items-center justify-between bg-white px-4 py-3 border-t border-gray-200 sm:px-6 rounded-lg shadow-sm mt-6">
              <div className="flex justify-between w-full">
                <button
                    onClick={() => setPage((p) => Math.max(1, p - 1))}
                    disabled={page === 1}
                    className="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                >
                  Précédent
                </button>
                <span className="text-sm text-gray-700 self-center">
              Page <span className="font-medium">{data.pagination.page}</span>{' '}
                  sur <span className="font-medium">{data.pagination.pages}</span>
            </span>
                <button
                    onClick={() => setPage((p) => Math.min(data.pagination.pages, p + 1))}
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