import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import ReservationsPage from '@/app/reservations/page';
import { reservationService } from '@/services/reservation.service';

jest.mock('@/services/reservation.service');

const mockReservations = {
  data: [
    {
      id: 'res-123',
      session: {
        id: 'ses-456',
        language: 'TOEFL',
        startsAt: '2026-11-20T14:00:00Z',
        location: 'Centre Lyon',
        capacity: 10,
      },
      user: { id: 'usr-1', name: 'Test User', email: 'test@test.com' },
      createdAt: '2026-06-01T10:00:00Z'
    }
  ],
  meta: {
    total: 1,
    page: 1,
    limit: 6,
    pages: 1
  }
};

describe('Page Mes Réservations', () => {
  beforeEach(() => {
    jest.clearAllMocks();
    // On intercepte window.confirm pour qu'il simule un clic sur "OK"
    window.confirm = jest.fn(() => true);
  });

  it('affiche la liste des réservations après chargement', async () => {
    (reservationService.getUserReservations as jest.Mock).mockResolvedValue(mockReservations);

    render(<ReservationsPage />);

    expect(screen.getByText(/Chargement de vos réservations.../i)).toBeInTheDocument();

    await waitFor(() => {
      expect(screen.getByText('TOEFL')).toBeInTheDocument();
    });

    expect(screen.getByText('Centre Lyon')).toBeInTheDocument();
    expect(screen.getByText('Confirmée')).toBeInTheDocument();
  });

  it('affiche le message vide si aucune réservation', async () => {
    (reservationService.getUserReservations as jest.Mock).mockResolvedValue({ 
      data: [], 
      meta: { total: 0, page: 1, limit: 6, pages: 0 } 
    });

    render(<ReservationsPage />);

    await waitFor(() => {
      expect(screen.getByText(/Vous n'avez aucune réservation pour le moment./i)).toBeInTheDocument();
    });
  });

  it('appelle le service d\'annulation après confirmation', async () => {
    (reservationService.getUserReservations as jest.Mock).mockResolvedValue(mockReservations);
    (reservationService.cancelReservation as jest.Mock).mockResolvedValue({ success: true });

    render(<ReservationsPage />);

    await waitFor(() => {
      expect(screen.getByText('TOEFL')).toBeInTheDocument();
    });

    const cancelButton = screen.getByRole('button', { name: /Annuler la réservation/i });
    fireEvent.click(cancelButton);

    expect(window.confirm).toHaveBeenCalledWith('Voulez-vous vraiment annuler cette réservation ?');
    
    await waitFor(() => {
      expect(reservationService.cancelReservation).toHaveBeenCalledWith('res-123');
    });
  });
});