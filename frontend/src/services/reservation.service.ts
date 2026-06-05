import { apiClient } from '@/lib/axios';

export const reservationService = {
  async createReservation(sessionId: string) {
    const response = await apiClient.post('/reservations', { sessionId });
    return response.data;
  }
};