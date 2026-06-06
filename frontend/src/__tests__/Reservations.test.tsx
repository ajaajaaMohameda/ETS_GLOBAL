import { render, screen, waitFor, fireEvent } from '@testing-library/react';
import ReservationsPage from '@/app/reservations/page';
import { reservationService } from '@/services/reservation.service';

jest.mock('@/services/reservation.service');
jest.mock('next/link', () => ({
  __esModule: true,
  default: ({ children, href }: { children: React.ReactNode; href: string }) => (
    <a href={href}>{children}</a>
  ),
}));

const mockReservations = {
  data: [
    {
      id: 'res-123',
      sessionId: 'ses-456',
      language: 'TOEFL',
      location: 'Centre Lyon',
      reservedAt: '2026-06-01T10:00:00Z',
    },
  ],
  pagination: {
    total: 1,
    page: 1,
    limit: 6,
    pages: 1,
  },
};

describe('Page Mes Réservations', () => {
  beforeEach(() => {
    jest.clearAllMocks();
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
      pagination: { total: 0, page: 1, limit: 6, pages: 0 },
    });

    render(<ReservationsPage />);

    await waitFor(() => {
      expect(
        screen.getByText(/Vous n'avez aucune réservation pour le moment./i)
      ).toBeInTheDocument();
    });
  });

  it("appelle le service d'annulation après confirmation", async () => {
    (reservationService.getUserReservations as jest.Mock).mockResolvedValue(mockReservations);
    (reservationService.cancelReservation as jest.Mock).mockResolvedValue({ success: true });

    render(<ReservationsPage />);

    await waitFor(() => {
      expect(screen.getByText('TOEFL')).toBeInTheDocument();
    });

    const cancelButton = screen.getByRole('button', { name: /Annuler la réservation/i });
    fireEvent.click(cancelButton);

    expect(window.confirm).toHaveBeenCalledWith(
      'Voulez-vous vraiment annuler cette réservation ?'
    );

    await waitFor(() => {
      expect(reservationService.cancelReservation).toHaveBeenCalledWith('res-123');
    });
  });

  it("affiche un message d'erreur si le chargement échoue", async () => {
    (reservationService.getUserReservations as jest.Mock).mockRejectedValue(
      new Error('Network error')
    );

    render(<ReservationsPage />);

    await waitFor(() => {
      expect(
        screen.getByText(/Erreur lors du chargement de vos réservations./i)
      ).toBeInTheDocument();
    });
  });

  it("affiche un message d'erreur si l'annulation échoue", async () => {
    (reservationService.getUserReservations as jest.Mock).mockResolvedValue(mockReservations);
    (reservationService.cancelReservation as jest.Mock).mockRejectedValue(
      new Error('Cancel error')
    );

    render(<ReservationsPage />);

    await waitFor(() => {
      expect(screen.getByText('TOEFL')).toBeInTheDocument();
    });

    fireEvent.click(screen.getByRole('button', { name: /Annuler la réservation/i }));

    await waitFor(() => {
      expect(
        screen.getByText(/Erreur lors de l'annulation. Veuillez réessayer./i)
      ).toBeInTheDocument();
    });
  });
});