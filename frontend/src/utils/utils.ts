import type { IncomingOutgoingPayments, Transactions } from '../types/payments.interfaces';

// Function to parse incoming and outgoing payments into transactions
export const parseIncomingOutgoingToTransactions = (incomingOutgoingPayments: IncomingOutgoingPayments[] = [], expectedType: string): Transactions[] => {
  // Adding incoming and outgoing payments to the transactions
  let totalTransactions: Transactions[] = [];

  // Adding incoming payments if they exits
  if (incomingOutgoingPayments.length > 0) {
    totalTransactions = [...totalTransactions, ...incomingOutgoingPayments.flatMap((payment) => payment.status === 'PENDING' ? ({
      id: payment.id,
      type: expectedType,
      amount: payment.amount,
      referenceId: null,
      created_at: payment.created_at,
    }) : [])];
  }

  // If there are transactions we order them by created_at
  if (totalTransactions.length > 0) {
    totalTransactions.sort(
      (a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime()
    );
  }

  // Return the combined transactions
  return totalTransactions;
};
