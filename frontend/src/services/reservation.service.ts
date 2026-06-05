import { apiClient } from '@/lib/axios';
import { PaginatedResponse, Reservation } from '@/types';

export const reservationService = {
  async createReservation(sessionId: string) {
    const response = await apiClient.post('/reservations', { sessionId });
    return response.data;
  },

  async getUserReservations(page: number = 1, limit: number = 10): Promise<PaginatedResponse<Reservation>> {
    const response = await apiClient.get('/reservations', {
      params: { page, limit }
    });
    return response.data;
  },

  async cancelReservation(reservationId: string) {
    const response = await apiClient.delete(`/reservations/${reservationId}`);
    return response.data;
  }
};